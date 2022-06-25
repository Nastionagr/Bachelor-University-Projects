import random
import os
import socket
import struct
import math
import sys
import time
import libscrc
from threading import Thread, Lock

#_____________________________________________CONSTANTS_____________________________________________#
bufferSize = 1472 # my header + max size of data (in bytes)
windowSize = 8 # a window for sending packets
indexL = 0 # left border of the window
indexR = 0 # right border of the window
headerSize = 10 # size of my own header
#_______________________________________________TYPE________________________________________________#
CONNECT = 0b1111
DISCONNECT = 0b0000
INIT = 0b1000
LAST = 0b0001
KEEP_ALIVE = 0b1001
TXT = 0b0101
FILE = 0b1010
#_______________________________________________FLAG________________________________________________#
ACK = 0b1111
NACK = 0b1001
#___________________________________________________________________________________________________#
SEND_ERROR = 0 # 0 - without errors, 1 - incorrect crc
C_CONNECTED = False # mark if the client is connected to the server

def createHeader(crc, typ, flag, sequenceNumber, length):
    TF = (typ & 0b1111) << 4 | (flag & 0b1111) # create a 1-byte parameter
    sequence = struct.pack(">I", sequenceNumber)[1:4] # resizing to the 3 bytes
    return struct.pack('!IB3sH', crc, TF, sequence, length) # I(4) c(1) 3s(sizeof(sequence)) H(2)

def decodeHeader(header):
    crc, TF, sequence, length = struct.unpack('!IB3sH', header) # decoding our header
    typ = TF >> 4 # get a type from 1 byte
    flag = TF & 0b1111 # get a flag from 1 byte
    sequenceNumber = int.from_bytes(sequence, "big") # get a sequenceNumber from the 3-byte array
    return crc, typ, flag, sequenceNumber, length

def sendMainInformation(serverIP, serverPort, clientSock, crc, typ, flag, sequenceNumber, length, name = False):
    infoHeader = createHeader(crc, typ, flag, sequenceNumber, length)
    if name != False:
        infoHeader += bytearray(name, encoding='utf-8')
    clientSock.sendto(infoHeader, (serverIP, serverPort))  # sending the info to the server
    data = clientSock.recv(bufferSize)  # getting feedback from the server
    crc_1, typ_1, flag_1, sequenceNumber_1, length_1 = decodeHeader(data[:headerSize])  # decoding the response
    while flag_1 != ACK:  # if server showed an error
        clientSock.sendto(infoHeader, (serverIP, serverPort))  # send the packet again
        data = clientSock.recv(bufferSize)
        crc_1, typ_1, flag_1, sequenceNumber_1, length_1 = decodeHeader(data[:headerSize])  # check the feedback again
    return data[headerSize:]

def receiveData(KASock, clientSock, information, lock):
    global indexL, indexR, C_CONNECTED
    while C_CONNECTED and indexL < len(information) and indexR <= len(information): # till 2 borders aren't at the end
        response = clientSock.recv(bufferSize)  # getting feedback from the server
        crc, typ, flag, sequenceNumber, length = decodeHeader(response[:headerSize])  # decoding the response

        lock.acquire()  # stop changing
        if flag == ACK: # server approved the packet
            if sequenceNumber-1 < indexL:
                print("Server approved already accepted ", sequenceNumber, " packet.")
            else:
                information[sequenceNumber-1][0] = 2 # change the status of the packet as 'received'
                print("Server approved ", sequenceNumber, " packet.")
                while sequenceNumber-1 < len(information) and information[sequenceNumber-1][0] == 2 and indexL == sequenceNumber-1: # move the window
                    indexL += 1
                    if indexR+1 <= len(information):
                        indexR += 1
                    sequenceNumber += 1
        elif flag == DISCONNECT:
            C_CONNECTED = False
            print("\nServer disconnected.")
            print("Disconnecting client...\n")
            KASock.close()
        else: # server disapproved the packet
            print("Server didn't approve ", sequenceNumber, " packet.")
            information[sequenceNumber - 1][0] = 0 # change the information for sending the packet again
            information[sequenceNumber - 1][1] = 0.0
        lock.release() # allow changing

def sendData(serverIP, serverPort, clientSock, information, data, typ, lock):
    global SEND_ERROR
    error = SEND_ERROR
    while C_CONNECTED and indexL < len(data) and indexR <= len(data): # till 2 borders aren't at the end
        lock.acquire()  # stop changing
        for i in range(indexL, indexR): # check the whole window
            if C_CONNECTED and (information[i][0] == 0 or (information[i][0] == 1 and time.time() - information[i][1] >= 0.2)): # if a packet wasn't sent or didn't get a feedback
                crc = libscrc.fsc(data[i]) # create a crc
                if error == 1: # send an incorrect crc (messed the packet up)
                    crc += 1
                    error = 0
                    print("Creating an incorrect CRC at ", i+1, " packet.")
                header = createHeader(crc, typ, 0, i+1, len(data[i])) # creating a header (i+1 - number of the current fragment)
                packet = header + data[i]
                try:
                    clientSock.sendto(packet, (serverIP, serverPort)) # sending packet
                except:
                    continue
                information[i][0] = 1 # change the status to 'sent'
                information[i][1] = time.time() # remember the time
                print("Sending ", i+1, " packet. It's size (header(10B) + data) is ", len(packet), "B.")
        lock.release() # allow changing

def startSending(serverIP, serverPort, KASock, clientSock, data, fragmentSize, file = False):
    global indexL, indexR

    start = time.time()

    #____starting communication____#
    pacData = []  # array of the fragments that have to be sent
    information = [] # array of information about the fragments

    if file == False: # if it is a message
        typ = TXT
        size = len(data)
        sendMainInformation(serverIP, serverPort, clientSock, 0, INIT, typ, size, fragmentSize) # send an initialization packet

        numberOfPackets = math.ceil(size / fragmentSize)
        index = 0
        for _ in range(0, numberOfPackets):  # dividing the whole message into pieces
            info = [0, 0.0]  # status(0 - not sent or has a mistake, 1 - sent, 2 - received), time
            information.append(info)
            pacData.append(data[index: index + fragmentSize]) # append the fragment of data
            index += fragmentSize
    else: # if it is a file
        typ = FILE
        size = os.path.getsize(file)
        name = file.split("\\")  # splitting the whole way
        sendMainInformation(serverIP, serverPort, clientSock, 0, INIT, typ, size, fragmentSize, name[len(name)-1]) # send an initialization packet
        numberOfPackets = math.ceil(size / fragmentSize)

        data = open(file, "rb")
        fragments = data.read(fragmentSize)
        while fragments: # dividing the whole file into pieces
            info = [0, 0.0]  # status(0 - not sent or has a mistake, 1 - sent, 2 - received), time
            information.append(info)
            pacData.append(fragments)
            fragments = data.read(fragmentSize)

        data.close()
    print("Server was informed about sending.")
    print("The total size of the data is ", size, "B. The data will be divided into ", numberOfPackets, "pieces.\n")

    # ____main body of the communication____#
    indexL = 0 # reseting left border of the window
    indexR = indexL + (windowSize if numberOfPackets > windowSize else numberOfPackets)# reseting right border of the window

    lock = Lock()
    sending = Thread(target=sendData, args=(serverIP, serverPort, clientSock, information, pacData, typ, lock), daemon = True)  # creating a thread for sending data
    receiving = Thread(target=receiveData, args=(KASock, clientSock, information, lock), daemon = True)  # creating a thread for receiving data
    sending.start()  # run a sending thread
    receiving.start()  # run a receiving thread
    sending.join()  # waits till the sending thread ends
    receiving.join()  # waits till the receiving thread ends

    # ____ending communication____#
    try:
        way = sendMainInformation(serverIP, serverPort, clientSock, 0, LAST, typ, 0, 0) # sending the last packet
    except:
        print("The client was disconnected.")
        return
    print("\nSending ended successfully. Total sending time is: ", time.time() - start, "s.")
    if typ == FILE:
        print("Server loaded all data to this directory: ", str(way, encoding='utf-8'))
    print()

    return

def maintainConnection(serverIP, serverPort, KASock):
    while C_CONNECTED == True:
        time.sleep(20) # every 20 s:

        keep_alive = createHeader(0, KEEP_ALIVE, 0, 1, 0) # create the first keep_alive
        try:
            KASock.sendto(keep_alive, (serverIP, serverPort))  # sending it to the server
        except: # if we can't send keep-alive - check if we are connected
            continue
        print("\nSending keep-alive №1 to the server.")
        KASock.settimeout(10) # waiting the response for a 10 s
        try:
            data = KASock.recv(bufferSize)  # getting feedback from the server
            continue # start the cycle again
        except:
            pass

        keep_alive = createHeader(0, KEEP_ALIVE, 0, 2, 0)  # create the second keep_alive
        try:
            KASock.sendto(keep_alive, (serverIP, serverPort))  # sending it to the server
        except: # if we can't send keep-alive - check if we are connected
            continue
        print("\nSending keep-alive №2 to the server.")
        KASock.settimeout(10)  # waiting the response for a 10 s
        try:
            data = KASock.recv(bufferSize)  # getting feedback from the server
            continue # start the cycle again
        except:
            pass

        print("\nServer disconnected.")
        print("Put 3 to disconnect or 0 to exit the client!\n")
        KASock.close()
        sys.exit()

def client():
    global C_CONNECTED, SEND_ERROR
    clientSock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM) # AF_INET - IPv4 SOCK_DGRAM - UDP
    clientSock.settimeout(3)
    KASock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM) # AF_INET - IPv4 SOCK_DGRAM - UDP

    while True:
        serverIP = input("Write IP address of the server:   ")
        serverPort = int(input("Write port of the server:   "))
        while C_CONNECTED == False:
            try:
                sendMainInformation(serverIP, serverPort, clientSock, 0, CONNECT, 0, 0, 0) # trying to connect to the server
                print("Client (IP address: %s Port: %d) is connected to the server."%(socket.gethostbyname(socket.gethostname()), clientSock.getsockname()[1]))
                C_CONNECTED = True # mark that connection has already started
            except:
                print("\nServer on this IP address or port does not exist. Try to put this data again...")
                serverIP = input("Write IP address of the server:   ")
                serverPort = int(input("Write port of the server:   "))

        maintaining = Thread(target=maintainConnection, args=(serverIP, serverPort, KASock), daemon=True)  # creating a thread for checking a keep_alive
        maintaining.start()  # run a keep_alive thread

        while C_CONNECTED == True:
            print("\n> (1) Send some text message.\n> (2) Send a file.\n> (3) Disconnect.\n> (0) Exit.")
            choice = int(input())
            if choice == 1:
                message = input("Here write your message: ")
                fragmentSize = int(input("Put the fragment's size (1-1462):   "))
                while fragmentSize < 1 or fragmentSize > 1462:
                    fragmentSize = int(input("Number has to be in range 1-1462. Try again:   "))
                SEND_ERROR = int(input("Print 0 if you don't want to have incorrect packet or 1 to mess up the CRC in one packet."))

                startSending(serverIP, serverPort, KASock, clientSock, message.encode('utf-8'), fragmentSize)

            elif choice == 2:
                file = input("Write the way to the file:   ")
                fragmentSize = int(input("Put the fragment's size (1-1462):   "))
                while fragmentSize < 1 or fragmentSize > 1462:
                    fragmentSize = int(input("Number has to be in range 1-1462. Try again:   "))
                SEND_ERROR = int(input("Print 0 if you don't want to have incorrect packet or 1 to mess up the CRC in one packet."))

                startSending(serverIP, serverPort, KASock, clientSock, 0, fragmentSize, file)

            elif choice == 3:
                C_CONNECTED = False  # mark that connection is ending
                maintaining.join()  # waits till the keep_alive thread ends
                try:
                    sendMainInformation(serverIP, serverPort, clientSock, 0, DISCONNECT, 0, 0, 0)  # telling the server that client wants to disconnect
                except:  # if no server is connected
                    pass
                print("Client is disconnected from the server.")
            elif choice == 0:
                C_CONNECTED = False  # mark that connection is ending
                maintaining.join()  # waits till the keep_alive thread ends
                try:
                    sendMainInformation(serverIP, serverPort, clientSock, 0, DISCONNECT, 0, 0, 0)  # telling the server that client wants to disconnect
                except:  # if no server is connected
                    pass
                print("Client is disconnected from the server.")
                print("Stop session.")
                clientSock.close()
                return
            else:
                print("You have put a wrong number. Try again.")

    return

def sendFeedback(serverSock, addr, crc, typ, flag, sequenceNumber, length, directory=False):
    response = createHeader(crc, typ, flag, sequenceNumber, length)  # marking a flag
    if directory != False:
        response += directory
    serverSock.sendto(response, addr)  # sending a feedback to the client

def receivePacket(serverSock, name=False):
    timer = 0
    while True:
        try:
            if timer > 35:  # if we didn't get any packet during 35 s
                print("\nClient disconnected.")
                print("Disconnecting server...\n")
                if name != False:
                    os.remove(name)
                serverSock.close()
                sys.exit()
            packet, addr = serverSock.recvfrom(bufferSize)  # getting a packet
            return packet, addr
        except socket.timeout:
            timer += 0.5
            continue

def server():
    port = int(input("Put a server's port (1025–65535):   "))
    while port < 1025 or port > 65535:
        port = int(input("The number has to be in range 1025–65535. Try again:   "))
    serverIP = socket.gethostbyname(socket.gethostname())

    serverSock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)  # AF_INET - IPv4 and SOCK_DGRAM - UDP
    serverSock.bind((serverIP, port))  # set a server's IP and its port
    serverSock.settimeout(0.5)
    print("Server's IP address : ", serverIP)
    print("Server is listening on the port : ", port)

    dir = int(input("Press 1 to use a default directory for saving files or 2 to set your own:   "))
    while dir != 1 and dir != 2:
        print("You have put a wrong number. Try again...")
        dir = int(input("Press 1 to use a default directory for saving files or 2 to set your own:   "))
    if dir == 1:
        directory = os.getcwd()
    else:
        directory = input("Write a new directory here:   ")
        while os.path.isdir(directory) == False:
            directory = input("Such directory does not exists. Try to write it again:   ")

    print("Press Ctrl+C+Enter if you want to disconnect server.\n")

    connected = False
    name = False
    try:
        while True:
            while connected == False:  # if the client is not connected
                while True:
                    try:
                        connectPacket, addr = serverSock.recvfrom(bufferSize)  # getting a connection packet
                        break
                    except socket.timeout:
                        continue
                crc, typ, flag, sequenceNumber, length = decodeHeader(connectPacket[:headerSize])  # decoding the information
                if typ == CONNECT:  # if smb wants to connect
                    sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                    C_IP = addr[0]
                    C_Port = addr[1]
                    print("Client (IP address: %s Port: %d) is connected to the server.\n" % (C_IP, C_Port))
                    connected = True

            while connected == True:
                firstPacket, addr = receivePacket(serverSock)  # getting the first packet from the client
                crc, typ, fileType, wholeSize, fragmentSize = decodeHeader(firstPacket[:headerSize])  # decoding the information

                if typ == INIT:
                    sendFeedback(serverSock, addr, crc, typ, ACK, wholeSize, fragmentSize)  # send a positive response
                    if fileType == FILE:
                        name = str(firstPacket[headerSize:], encoding='utf-8')  # getting the name of the file
                        print("Client is going to send a file called ", name)
                        print("The whole size of the file is ", wholeSize, "B.")
                        open(directory + "\\" + name, "w").close()  # future whole file from the client
                        file_array = {}  # a buffer (dictionary) for a file fragments
                    else:
                        print("Client is going to send a message.")
                        print("The whole size of the message is ", wholeSize, "B.")
                        text_message = ""  # future whole text message from the client
                        text_array = {}  # a buffer (dictionary) for a text
                    break
                elif typ == DISCONNECT:
                    sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                    print("Client (IP address: %s Port: %d) is disconnected from the server.\n" % (C_IP, C_Port))
                    connected = False
                elif typ == KEEP_ALIVE:
                    sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                    print("Keep_alive was received. Client (IP address: %s Port: %d) is still connected to the server." % (C_IP, C_Port))

            if connected == True:
                numberOfPackets = math.ceil(wholeSize / fragmentSize)
                print("Maximum fragment's size is ", fragmentSize, "B and the total number of the packets is ", numberOfPackets, ".")
                currentPacket = 1  # left border of the buffer
                window = currentPacket + (windowSize if numberOfPackets > windowSize else numberOfPackets)  # right border of the buffer

            while connected == True:
                packet, addr = receivePacket(serverSock, name)  # getting the packet from a client
                crc, typ, flag, sequenceNumber, length = decodeHeader(packet[:headerSize])  # decoding the packet
                data = packet[headerSize:]

                if typ == DISCONNECT:
                    sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                    print("Client (IP address: %s Port: %d) is disconnected from the server.\n" % (addr[0], addr[1]))
                    connected = False
                elif typ == LAST:
                    if flag == TXT:
                        sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                        print("Client sent the whole information.")
                        while currentPacket in text_array.keys():  # check the last missing packets
                            text_message += str(text_array[currentPacket], encoding='utf-8')  # add it to the final message
                            currentPacket += 1
                            text_array.pop(currentPacket)
                        print("\nReceived message:", text_message)
                    else:
                        sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length, bytearray(directory, encoding='utf-8'))  # send a positive response
                        print("Client sent the whole information.")
                        file = open(directory + "\\" + name, "ab")
                        while currentPacket in file_array.keys():  # check if there is a fragment that we are waiting for
                            file.write(file_array[currentPacket])  # add it to the final file
                            file_array.pop(currentPacket)
                            currentPacket += 1
                        file.close()
                        print("\nReceived file is located here: ", directory)
                    print()
                    break
                elif typ == KEEP_ALIVE:
                    sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response
                    print("Keep_alive was received. Client (IP address: %s Port: %d) is still connected to the server." % (C_IP, C_Port))
                else:  # txt or a file
                    new_crc = libscrc.fsc(data)  # create a new crc to combine
                    new_length = len(data)  # counting a new length
                    if new_crc != crc or new_length != length:
                        print("Fragment ", sequenceNumber, " is fault. Sending NACK.")
                        sendFeedback(serverSock, addr, crc, typ, NACK, sequenceNumber, length)  # send a negative response
                    else:
                        sendFeedback(serverSock, addr, crc, typ, ACK, sequenceNumber, length)  # send a positive response

                        if sequenceNumber < currentPacket:  # if it was already downloaded (can happend because of timeout)
                            print("Received already accepted ", sequenceNumber, " packet.")
                        elif sequenceNumber == currentPacket:  # if it is the one, we are waiting for
                            print("Fragment ", sequenceNumber, " was received correctly. It's size (header(10B) + data) is ", len(packet), "B.")
                            if typ == TXT:
                                text_message += str(data, encoding='utf-8')  # add it to the final message
                                currentPacket += 1
                                while currentPacket in text_array.keys():  # check if there is a fragment that we are waiting for
                                    text_message += str(text_array[currentPacket], encoding='utf-8')  # add it to the final message
                                    text_array.pop(currentPacket)
                                    currentPacket += 1
                            elif typ == FILE:
                                file = open(directory + "\\" + name, "ab")
                                file.write(data)  # add it to the final file
                                currentPacket += 1
                                while currentPacket in file_array.keys():  # check if there is a fragment that we are waiting for
                                    file.write(file_array[currentPacket])  # add it to the final file
                                    file_array.pop(currentPacket)
                                    currentPacket += 1
                                file.close()
                        elif sequenceNumber > currentPacket and sequenceNumber <= currentPacket + window:  # if we are waiting for another packet in a sequence
                            print("Fragment ", sequenceNumber, " was received correctly. It's size (header(10B) + data) is ", len(packet), "B.")
                            if typ == TXT:
                                text_array[sequenceNumber] = data
                            elif typ == FILE:
                                file_array[sequenceNumber] = data
                        else:  # if sender sent a packet bigger that its window (impossible ?!)
                            print("Watson, we have a problem.")
    except KeyboardInterrupt:
        print("\nDisconnecting server...")
        try:
            sendFeedback(serverSock, addr, crc, DISCONNECT, DISCONNECT, sequenceNumber, length)  # telling the client that we are going to disconnect
        except:  # if no client is connected
            pass
        if name != False:
            try: # if it was opened - close the file
                file.close()
            except:
                pass
            os.remove(name) # delete non-completed file
        serverSock.close()

    return

def main():
    random.seed()

    while True:
        print("> (1) Server\n> (2) Client\n> (0) Exit")
        choice = int(input())
        if choice == 1:
            server ()
        elif choice == 2:
            client()
        elif choice == 0:
            return 0
        else:
            print("You have put a wrong number. Try again.")

main()