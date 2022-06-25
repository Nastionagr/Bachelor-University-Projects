from scapy.all import *

CRED    = '\33[31m'
CYELLOW = '\33[33m'
CBEIGE  = '\33[36m'
CGREY    = '\33[90m'

RESET = '\33[0m'

print(CYELLOW + 'Zádajte cestu k pcap súboru:', RESET)
cesta = input()

#cesta = "C:/Users/Nastia/Desktop/STU/PKS/Zadanie_1/vzorky_pcap_na_analyzu/trace-26.pcap"

packets = rdpcap(cesta)
index = 0

for packet in packets:
    packet = bytes(packet)
    print()
    print(CBEIGE + 'Rámec', RESET, index+1)
    index = index + 1
    print(CBEIGE + 'dĺžka rámca poskytnutá pcap API:', RESET, len(packet), 'B')

    if len(packet)+4 > 64:
        print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, len(packet)+4, 'B')
    else:
        print(CBEIGE + 'dĺžka rámca prenášaného po médiu:', RESET, '64 B')

    if packet[12] << 8 | packet[13] > 1500:
        print('Ethernet II')
    elif packet[14] == 0xaa:
        print('IEEE 802.3 s LLC a SNAP')
    elif packet[14] == 0xff:
        print('IEEE 802.3 Raw')
    else:
        print('IEEE 802.3 s LLC')

    print(CBEIGE + 'zdrojová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[6], packet[7], packet[8], packet[9], packet[10], packet[11]))
    print(CBEIGE + 'cieľová MAC adresa:', RESET, '%.2x:%.2x:%.2x:%.2x:%.2x:%.2x'%(packet[0], packet[1], packet[2], packet[3], packet[4], packet[5]))

    info = 0
    for info in range(0, len(packet)):
        print(CGREY + '%.2x '%(packet[info]), end=RESET)
        info = info + 1
        if info%16 == 0:
            print()
    print()
    print(CBEIGE + '________________________________________________', RESET)