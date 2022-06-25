from scapy.all import *

CRED    = '\33[31m'
CGREEN  = '\33[32m'
CYELLOW = '\33[33m'
CBLUE   = '\33[34m'
CVIOLET = '\33[35m'
CBEIGE  = '\33[36m'
CWHITE  = '\33[37m'
CGREY    = '\33[90m'
RESET = '\33[0m'

#print(CYELLOW + 'Zádajte cestu k pcap súboru:', RESET)
#cesta = input()
cesta = "C:/Users/Nastia/Desktop/STU/PKS/Zadanie_1/vzorky_pcap_na_analyzu/trace-27.pcap"
packets = rdpcap(cesta) #input packets of information

PROTOCOL = [] #information from the Types.txt
file = open("Types.txt", "r")
types = file.read().split('#') #split all blocks
for _type in types[1:]: #the first one is empty, cauze I have '#' at the beginning of the Types.txt
    array_line = _type.split('\n') #split lines
    helper = {}
    for arr in array_line[1:]: #the first one is blocks' name
        array_word = arr.split(' ') #split hex&name
        for arr_index in range(int(len(array_word)/2)):
            helper[int(array_word[2*arr_index], base=16)] = array_word[2*arr_index+1]
    PROTOCOL.append(helper)

def nested_protocol (hex_number, index_arr):
    if hex_number in PROTOCOL[index_arr].keys(): #if hex_number is written in Types.txt
        protocol_name = PROTOCOL[index_arr][hex_number]
        return protocol_name
    else:
        return None

########################################################################################################################

def write_packets (index, source, dest, packets):

    TYP = {0: 'Echo Reply', 3: 'Destination Unreachable', 4: 'Source Quench', 5: 'Redirect', 8: 'Echo',
           9: 'Router Advertisement', 10: 'Router Selection', 11: 'Time Exceeded', 12: 'Parameter Problem',
           13: 'Timestamp', 14: 'Timestamp Reply', 15: 'Information Request', 16: 'Information Reply',
           17: 'Address Mask Request', 18: 'Address Mask Reply', 30: 'Traceroute'}

    array = []
    if len(packets)>20:
        for i in range (0,10):
            array.append(packets[i])
        for i in range ((len(packets)-10), len(packets)):
            array.append(packets[i])
    else:
        array = packets

    for arr in array:
        print()
        print(CBEIGE + 'Rámec', RESET, arr[0])
        print(CBEIGE + 'dĺžka rámca poskytnutá pcap API:', RESET, len(arr[1]), 'B')
        if len(arr[1]) + 4 > 64:
            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, len(arr[1]) + 4, 'B')
        else:
            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, '64 B')
        print('Ethernet II')
        print(CBEIGE + 'zdrojová MAC adresa:', RESET,'%.2x:%.2x:%.2x:%.2x:%.2x:%.2x' % (arr[1][6], arr[1][7], arr[1][8], arr[1][9], arr[1][10], arr[1][11]))
        print(CBEIGE + 'cieľová MAC adresa:', RESET,'%.2x:%.2x:%.2x:%.2x:%.2x:%.2x' % (arr[1][0], arr[1][1], arr[1][2], arr[1][3], arr[1][4], arr[1][5]))

        if index == 1:
            print('IPv4')
            print(CBEIGE + 'zdrojová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][26], arr[1][27], arr[1][28], arr[1][29]))
            print(CBEIGE + 'cieľová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][30], arr[1][31], arr[1][32], arr[1][33]))
            print('TCP')
            print(CBEIGE + 'zdrojový port:', RESET, source)
            print(CBEIGE + 'cieľový port:', RESET, dest)
        elif index == 2:
            print('IPv4')
            print(CBEIGE + 'zdrojová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][26], arr[1][27], arr[1][28], arr[1][29]))
            print(CBEIGE + 'cieľová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][30], arr[1][31], arr[1][32], arr[1][33]))
            print('UDP')
            print(CBEIGE + 'zdrojový port:', RESET, source)
            print(CBEIGE + 'cieľový port:', RESET, dest)
        elif index == 3:
            print('IPv4')
            print(CBEIGE + 'zdrojová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][26], arr[1][27], arr[1][28], arr[1][29]))
            print(CBEIGE + 'cieľová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][30], arr[1][31], arr[1][32], arr[1][33]))
            print('ICMP')
            IHL = 4 * (arr[1][14] & 0b1111)
            typ = arr[1][14 + IHL]
            print(CBEIGE + 'typ:', RESET, TYP[typ])
        elif index == 4:
            print('ARP')
            operation = arr[1][20] << 8 | arr[1][21]
            if operation == 1:
                print('ARP-Request')
                print(CBEIGE + 'IP adresa:', RESET, '%d.%d.%d.%d' %(arr[1][38], arr[1][39], arr[1][40], arr[1][41]))
                print(CBEIGE + 'MAC adresa:', RESET, '???')
            else:
                print('ARP-Reply')
                print(CBEIGE + 'IP adresa:', RESET, '%d.%d.%d.%d' %(arr[1][28], arr[1][29], arr[1][30], arr[1][31]))
                print(CBEIGE + 'cieľová MAC adresa:', RESET,'%.2x:%.2x:%.2x:%.2x:%.2x:%.2x' % (arr[1][22], arr[1][23], arr[1][24], arr[1][25], arr[1][26], arr[1][27]))

            print(CBEIGE + 'zdrojová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][28], arr[1][29], arr[1][30], arr[1][31]))
            print(CBEIGE + 'cieľová IP adresa:', RESET,'%d.%d.%d.%d' % (arr[1][38], arr[1][39], arr[1][40], arr[1][41]))

        # printing all hex data
        for info in range(0, len(arr[1])):
            print(CGREY + '%.2x ' % (arr[1][info]), end=RESET)
            info = info + 1
            if info % 16 == 0:
                print()
        print()
        print(CBEIGE + '________________________________________________', RESET)

    print()

def write_TCP (communication):

    neuplna = 0
    uplna = 0
    for com in communication:
        if neuplna == 0 and com[4] == True and com[5] == False:
            print()
            print(CVIOLET + 'Prvá neuplna komunikacia:', RESET)
            write_packets(1, com[2], com[3], com[6])
            neuplna = 1

        if uplna == 0 and com[4] == True and com[5] == True:
            print()
            print(CVIOLET + 'Príklad uplnej komunikacie:', RESET)
            write_packets(1, com[2], com[3], com[6])
            uplna = 1

        if neuplna == 1 and uplna == 1:
            break

    if neuplna == 0:
        print()
        print(CVIOLET + 'Ziadna neuplna komunikacia tu nie je.', RESET)
    if uplna == 0:
        print()
        print(CVIOLET + 'Ziadna uplna komunikacia tu nie je.', RESET)

def write_UDP (communication):

    index = 1
    for com in communication:
        print()
        print(CVIOLET, index, 'KOMUNIKACIA', RESET)
        index = index + 1
        write_packets (2, com[2], com[3], com[4])

def write_ICMP (communication):

    index = 1
    for com in communication:
        print()
        print(CVIOLET, index, 'KOMUNIKACIA', RESET)
        index = index + 1
        write_packets(3, 0, 0, com[2])

def write_ARP (communication):

    index = 1
    for com in communication:
        print()
        print(CVIOLET, index, 'KOMUNIKACIA', RESET)
        index = index + 1
        write_packets(4, 0, 0, com[3])

def print_comunications (communication_TCP, communication_UDP, communication_ICMP, communication_ARP):

    #________________________________________________________TCP_______________________________________________________#
    if len(communication_TCP['SSH']) != 0:
        print()
        print(CRED + 'SSH COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['SSH'])
    if len(communication_TCP['HTTP']) != 0:
        print()
        print(CRED + 'HTTP COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['HTTP'])
    if len(communication_TCP['HTTPS']) != 0:
        print()
        print(CRED + 'HTTPS COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['HTTPS'])
    if len(communication_TCP['TELNET']) != 0:
        print()
        print(CRED + 'TELNET COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['TELNET'])
    if len(communication_TCP['FTP_control']) != 0:
        print()
        print(CRED + 'FTP_control COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['FTP_control'])
    if len(communication_TCP['FTP_data']) != 0:
        print()
        print(CRED + 'FTP_data COMMUNICATIONS:', RESET)
        write_TCP (communication_TCP['FTP_data'])

    #________________________________________________________UDP_______________________________________________________#
    if len(communication_UDP['TFTP']) != 0:
        print()
        print(CRED + 'TFTP COMMUNICATIONS:', RESET)
        write_UDP (communication_UDP['TFTP'])

    #_______________________________________________________ICMP_______________________________________________________#
    if len(communication_ICMP['PING']) != 0:
        print()
        print(CRED + 'PING COMMUNICATIONS:', RESET)
        write_ICMP (communication_ICMP['PING'])

    if len(communication_ICMP['OTHERS']) != 0:
        print()
        print(CRED + 'OTHER COMMUNICATIONS:', RESET)
        write_ICMP (communication_ICMP['OTHERS'])

    #________________________________________________________ARP_______________________________________________________#
    if len(communication_ARP['COMMUNICATION']) != 0:
        print()
        print(CRED + 'ARP COMMUNICATIONS:', RESET)
        write_ARP (communication_ARP['COMMUNICATION'])

########################################################################################################################

def bod_3 (packet, destination_ip):
    nested = nested_protocol(packet[23], 2) #search in IP block
    if nested:
        print(nested) #name of the nested protocol
    print(CBEIGE + 'zdrojová IP adresa:', RESET, '%d.%d.%d.%d'%(packet[26], packet[27], packet[28], packet[29]))
    print(CBEIGE + 'cieľová IP adresa:', RESET, '%d.%d.%d.%d'%(packet[30], packet[31], packet[32], packet[33]))

    dest_ip = packet[30]<<24 | packet[31]<<16 | packet[32]<<8 | packet[33]
    index = 0
    for _ip in destination_ip.keys(): #searching our ip among others
        if dest_ip == _ip: #if we find one, it isn't a unique one (it has already been in list)
            destination_ip[dest_ip] = destination_ip[dest_ip] + 1 #enlarge its amount
            index=1
            break
    if index == 0:
        destination_ip[dest_ip] = 1 #ip is unique, creating a new item

def bod_1_2_3 ():

    index = 1 #the number of the ramec
    destination_ip = {}
    for packet in packets:
        packet = bytes(packet)
        print()
        print(CBEIGE + 'Rámec', RESET, index)
        index = index + 1

        print(CBEIGE + 'dĺžka rámca poskytnutá pcap API:', RESET, len(packet), 'B')
        if len(packet)+4 > 64:
            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, len(packet)+4, 'B')
        else:
            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, '64 B')

        if packet[12] << 8 | packet[13] > 1500:
            print('Ethernet II')
            print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
            print(CBEIGE + 'cieľová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))
            nested = nested_protocol(packet[12] << 8 | packet[13], 0)
            if nested:
                print(nested)
                if nested == 'IPv4':
                    bod_3(packet, destination_ip)

        elif packet[14] == 0xaa:
            print('IEEE 802.3 s LLC a SNAP')
            print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
            print(CBEIGE + 'cieľová MAC adresa:', RESET,'%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))
            nested = nested_protocol(packet[20] << 8 | packet[21], 0)
            if nested:
                print(nested)

        elif packet[14] == 0xff:
            print('IEEE 802.3 Raw')
            print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
            print(CBEIGE + 'cieľová MAC adresa:', RESET,'%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))
            print('IPX')

        else:
            print('IEEE 802.3 s LLC')
            print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
            print(CBEIGE + 'cieľová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))
            nested = nested_protocol(packet[14], 1)
            if nested:
                print(nested)

        #printing all hex data of the packet
        for info in range(0, len(packet)):
            print(CGREY + '%.2x '%(packet[info]), end=RESET)
            info = info + 1
            if info%16 == 0:
                print()
        print()
        print(CBEIGE + '________________________________________________', RESET)

    print()
    if destination_ip: #printing the list of unique ip addresses

        if len(destination_ip)>1:
            maximum = destination_ip[next(iter(destination_ip))] #the max count is in second item
        else:
            maximum = destination_ip.values()

        print(CBEIGE + 'Zoznam IP adries všetkých prijímajúcich uzlov:', RESET)
        for keys in destination_ip.keys():
            print("%d.%d.%d.%d" %(keys >> 24, (keys >> 16)&0xFF, (keys >> 8)&0xFF, keys & 0xFF))
            maximum = max(maximum, destination_ip[keys]) #searching the max count of unique ip

        print(CBEIGE + 'adresa uzla(-ov) s najväčším počtom (', RESET, maximum, CBEIGE, ') odoslaných paketov:', RESET)
        for keys in destination_ip.keys():
            if maximum == destination_ip[keys]:
                print("%d.%d.%d.%d" % (keys >> 24, (keys >> 16) & 0xFF, (keys >> 8) & 0xFF, keys & 0xFF))

def bod_4():
    index = 1
    communication_TCP = {'SSH': [], 'HTTP': [], 'HTTPS': [], 'TELNET': [], 'FTP_control': [], 'FTP_data': []}
    communication_UDP = {'TFTP': []}
    communication_ICMP = {'PING': [], 'OTHERS': []}
    communication_ARP = {'COMMUNICATION': []}

    for packet in packets:

        packet = bytes(packet)
        packet_number = index
        index = index+1

        if packet[12] << 8 | packet[13] > 1500: #if it is Ethernet II
            nested_into_Ethernet = nested_protocol(packet[12] << 8 | packet[13], 0)
            if nested_into_Ethernet:

                if nested_into_Ethernet == 'IPv4':
                    packet_dIP = (packet[30], packet[31], packet[32], packet[33])
                    packet_sIP = (packet[26], packet[27], packet[28], packet[29])

                    nested_into_IPv4 = nested_protocol(packet[23], 2)
                    if nested_into_IPv4:
                        IHL = 4*(packet[14] & 0b1111)

                        if nested_into_IPv4 == 'TCP':

                            source_port = packet[14+IHL] << 8 | packet[14+IHL+1]
                            dest_port = packet[14+IHL+2] << 8 | packet[14+IHL+3]
                            if source_port < dest_port:
                                nested_into_TCP = nested_protocol(source_port, 3)
                            else:
                                nested_into_TCP = nested_protocol(dest_port, 3)

                            if nested_into_TCP:

                                if len(communication_TCP[nested_into_TCP]) == 0: #if the list is empty
                                    pac = [(packet_number, packet)]
                                    communication_TCP[nested_into_TCP].append([packet_dIP, packet_sIP, source_port, dest_port, False, False, pac])
                                else:
                                    i = 0
                                    for different_communication in communication_TCP[nested_into_TCP]:
                                        if (((packet_sIP==different_communication[1] and packet_dIP==different_communication[0]) or
                                        (packet_sIP==different_communication[0] and packet_dIP==different_communication[1]))
                                        and
                                        ((source_port==different_communication[2] and dest_port==different_communication[3]) or
                                        (source_port==different_communication[3] and dest_port==different_communication[2]))
                                        and
                                        different_communication[5] == False):
                                            i = 1
                                            (different_communication[6]).append((packet_number, packet)) #add a new packet to the communication

                                            #controlling FLAGS and handshakes
                                            FLAGS = packet[14+IHL+13]

                                            if (FLAGS&(0x1<<2))==1: #RST
                                                different_communication[5]=True

                                            if len(different_communication[6]) >= 3: #we have 3 items in our communication

                                                end_handshake = 0

                                                #finished
                                                for search in range (len(different_communication[6])):
                                                    FLAGS = different_communication[6][search][1][14 + IHL + 13]
                                                    if end_handshake == 0 and (FLAGS&0x1!= 0): #FIN
                                                        end_handshake = 1
                                                    elif end_handshake == 1 and (FLAGS&(0x1<<4) != 0) and (FLAGS&0x1 != 0):  #ACK,FIN
                                                        end_handshake = 2
                                                    elif end_handshake == 2 and FLAGS&(0x1<<4) != 0: #ACK
                                                        different_communication[5] = True

                                                #opened
                                                for search in range (len(different_communication[6])):
                                                    FLAGS = different_communication[6][search][1][14 + IHL + 13]
                                                    if (search+2<len(different_communication[6])) and (FLAGS&(0x1<<1) != 0):  # SYN
                                                        FLAGS = different_communication[6][search + 1][1][14 + IHL + 13]
                                                        if (FLAGS&(0x1<<4) != 0) and (FLAGS&(0x1<<1) != 0):  # ACK,SYN
                                                            FLAGS = different_communication[6][search + 2][1][14 + IHL + 13]
                                                            if FLAGS&(0x1<<4) != 0: #ACK
                                                                different_communication[4] = True

                                    if i == 0: #creating new communication if our packet didn't find one
                                        pac1 =[(packet_number, packet)]
                                        communication_TCP[nested_into_TCP].append([packet_dIP, packet_sIP, source_port, dest_port, False, False, pac1])

                        elif nested_into_IPv4 == 'UDP':
                            source_port = packet[14 + IHL] << 8 | packet[14 + IHL + 1]
                            dest_port = packet[14 + IHL + 2] << 8 | packet[14 + IHL + 3]
                            opcode = packet[14 + IHL + 8] << 8 | packet[14 + IHL + 8 + 1]

                            if opcode == 1 or opcode == 2:
                                pac = [(packet_number, packet)]
                                communication_UDP['TFTP'].append([packet_dIP, packet_sIP, source_port, dest_port, pac])
                            else:
                                for request in communication_UDP['TFTP']:
                                    if len(request[4]) == 1: #if it is without its pair
                                        if (((packet_dIP==request[0] and packet_sIP==request[1]) or
                                            (packet_dIP==request[1] and packet_sIP==request[0])) and
                                            (dest_port==request[2] or dest_port==request[3])):
                                            request[4].append((packet_number, packet))
                                            request[3] = source_port
                                            break
                                    else:
                                        if (((packet_dIP==request[0] and packet_sIP==request[1]) or
                                            (packet_dIP==request[1] and packet_sIP==request[0])) and
                                            ((source_port==request[2] and dest_port==request[3]) or
                                            (source_port==request[3] and dest_port==request[2]))):
                                            request[4].append((packet_number, packet))
                                            break

                        elif nested_into_IPv4 == 'ICMP':
                            TYPE = packet[14 + IHL]
                            pac = [(packet_number, packet)]

                            if TYPE == 0 or TYPE == 8:
                                identifier = packet[14 + IHL + 4] << 8 | packet[14 + IHL + 4 + 1]
                                sequence_number = packet[14 + IHL + 4 + 2] << 8 | packet[14 + IHL + 4 + 3]

                                if len(communication_ICMP['PING']) == 0:
                                    communication_ICMP['PING'].append([identifier, sequence_number, pac])
                                else:
                                    i = 0
                                    for communication in communication_ICMP['PING']:
                                        if len(communication[2]) == 1:
                                            if identifier == communication[0] and sequence_number == communication[1]:
                                                communication[2].append((packet_number, packet))
                                                i = 1
                                                break
                                    if i == 0:
                                        communication_ICMP['PING'].append([identifier, sequence_number, pac])
                            else:
                                if len(communication_ICMP['OTHERS']) == 0:
                                    communication_ICMP['OTHERS'].append([packet_dIP, packet_sIP, pac])
                                else:
                                    i = 0
                                    for communication in communication_ICMP['OTHERS']:
                                        if ((packet_dIP == communication[0] and packet_sIP == communication[1]) or
                                            (packet_dIP == communication[1] and packet_sIP == communication[0])):
                                            communication[2].append((packet_number, packet))
                                            i = 1
                                            break
                                    if i == 0:
                                        communication_ICMP['OTHERS'].append([packet_dIP, packet_sIP, pac])

                elif nested_into_Ethernet == 'ARP':
                    operation = packet[20] << 8 | packet[21]
                    packet_sIP = (packet[28], packet[29], packet[30], packet[31])
                    packet_dIP = (packet[38], packet[39], packet[40], packet[41])
                    pac = [(packet_number, packet)]

                    if len(communication_ARP['COMMUNICATION']) == 0:
                        communication_ARP['COMMUNICATION'].append([packet_sIP, packet_dIP, operation, pac])
                    else:
                        i = 0
                        for com in communication_ARP['COMMUNICATION']:
                            if (com[2] != 2 and ((packet_sIP == com[0] and packet_dIP == com[1]) or
                                (packet_sIP == com[1] and packet_dIP == com[0]))):
                                    com[3].append((packet_number, packet))
                                    if operation == 2:  # if it is a reply
                                        com[2] = 2 #complete the communication
                                    i = 1
                                    break
                        if i == 0:
                            communication_ARP['COMMUNICATION'].append([packet_sIP, packet_dIP, operation, pac])

    print_comunications(communication_TCP, communication_UDP, communication_ICMP, communication_ARP)

def bod_5():

    index = 1
    counter = 0
    for packet in packets:
        packet = bytes(packet)

        if packet[12] << 8 | packet[13] > 1500:
            nested = nested_protocol(packet[12] << 8 | packet[13], 0)
            if nested == 'IPv4':
                nested_into_IPv4 = nested_protocol(packet[23], 2)
                if nested_into_IPv4 == 'UDP':
                    IHL = 4 * (packet[14] & 0b1111)
                    source_port = packet[14 + IHL] << 8 | packet[14 + IHL + 1]
                    dest_port = packet[14 + IHL + 2] << 8 | packet[14 + IHL + 3]

                    if source_port < dest_port:
                        port = source_port
                    else:
                        port = dest_port

                    if port == 520: #RIP
                        counter = counter + 1
                        print()
                        print(CBEIGE + 'Rámec', RESET, index)

                        print(CBEIGE + 'dĺžka rámca poskytnutá pcap API:', RESET, len(packet), 'B')
                        if len(packet) + 4 > 64:
                            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, len(packet) + 4, 'B')
                        else:
                            print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, '64 B')

                        print('Ethernet II')
                        print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x' % (packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
                        print(CBEIGE + 'cieľová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x' % (packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))
                        print('IPv4')
                        print('UDP')
                        print(CBEIGE + 'zdrojová IP adresa:', RESET,'%d.%d.%d.%d' % (packet[26], packet[27], packet[28], packet[29]))
                        print(CBEIGE + 'cieľová IP adresa:', RESET,'%d.%d.%d.%d' % (packet[30], packet[31], packet[32], packet[33]))

                        for info in range(0, len(packet)):
                            print(CGREY + '%.2x ' % (packet[info]), end=RESET)
                            info = info + 1
                            if info % 16 == 0:
                                print()
                        print()
                        print(CBEIGE + '________________________________________________', RESET)
    index = index + 1

    if counter == 0:
        print(CBEIGE + 'Ziaden RIP port nebol najden.', RESET)
    else:
        print(CBEIGE + 'Dokopy:', RESET, counter, CBEIGE, 'portov.', RESET)

########################################################################################################################
print(CYELLOW + 'Zvoľte si typ analizu súboru:', RESET)
print(CRED + '1_2_3', RESET, CYELLOW, '- vypís všeobečnej informacie o rámcoch a ich vnorených protokoloch', RESET)
print(CRED + '4', RESET, CYELLOW, '- vypís komunikacie medzi rámcami', RESET)
print(CRED + '5', RESET, CYELLOW, '- doimplementacia', RESET)

your_choice = input()

if your_choice == '1_2_3':
    bod_1_2_3()
elif your_choice == '4':
    bod_4()
elif your_choice == '5':
    bod_5()
else:
    print(CRED + 'Zle zadané číslo', RESET)