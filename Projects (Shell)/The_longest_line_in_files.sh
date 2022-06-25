#! /bin/bash
#
# Meno: Anastasiia-Solomiia Hrytsyna
# Kruzok: J. Lastinec - stvrtok(14:00-16:00)
# Datum: 8.12.2020
# Zadanie: zadanie02
#
# Text zadania:
#
# V zadanych textovych suboroch uvedenych ako argumenty najdite najdlhsi riadok
# (riadky) zo vsetkych a vypiste ho (ich). Dlzka riadku je jeho dlzka v znakoch.
# Ak nebude uvedeny ako argument ziadny subor, prehladava sa standardny vstup
# (a jeho meno je -).
#
# Syntax:
# zadanie.sh [-h] [cesta ...]
#
# Vystup ma tvar:
# Output: '<subor>: <cislo riadku v subore> <dlzka riadku> <riadok>'
#
# Priklad vystupu (parametrami boli subory nahodny ine/lorem_ipsum
# v adresari /public/testovaci_adresar/testdir2):
# Output: 'ine/lorem_ipsum: 11 98 eu ipsum. Aliquam viverra vestibulum pretium...
# Output: 'nahodny: 3 98 UtRybYIDDPudgG!YUC?NTpgo,M!vsb.wFrTQtoacxOxnQtDVDzOfnPad...
# Output: 'nahodny: 4 98 UtRybYIDDPudgG!YUC?NTpgo,M!vsb.wFrTQtoacxOxnQtDVDzOfnPad...
#
#
# Program musi osetrovat pocet a spravnost argumentov. Program musi mat help,
# ktory sa vypise pri zadani argumentu -h a ma tvar:
# Meno programu (C) meno autora
#
# Usage: <meno_programu> <arg1> <arg2> ...
#       <arg1>: xxxxxx
#       <arg2>: yyyyy
#
# Parametre uvedene v <> treba nahradit skutocnymi hodnotami.
# Ked ma skript prehladavat adresare, tak vzdy treba prehladat vsetky zadane
# adresare a vsetky ich podadresare do hlbky.
# Pri hladani maxim alebo minim treba vzdy najst maximum (minimum) vo vsetkych
# zadanych adresaroch (suboroch) spolu. Ked viacero suborov (adresarov, ...)
# splna maximum (minimum), treba vypisat vsetky.
#
# Korektny vystup programu musi ist na standardny vystup (stdout).
# Chybovy vystup programu by mal ist na chybovy vystup (stderr).
# Chybovy vystup musi mat tvar (vratane apostrofov):
# Error: 'adresar, subor, ... pri ktorom nastala chyba': popis chyby ...
# Ak program pouziva nejake pomocne vypisy, musia mat tvar:
# Debug: vypis .
#
# Poznamky: (sem vlozte pripadne poznamky k vypracovanemu zadaniu)
#
# Riesenie:

if [ "$#" -eq 0 ]; then #if there isn't any arguments
   echo "Debug: Reading from the stdin...(use Ctrl+d to end reading)"

   if [ -f /tmp/stdin.txt ]; then #if the file already exist - reset it
      > /tmp/stdin.txt
   else #or create a new one
      touch /tmp/stdin.txt
   fi

   while IFS= read -r line; do #read the whole line
      echo "$line" >> /tmp/stdin.txt #and write it to the stdin.txt
   done

   LEN=$(wc -L /tmp/stdin.txt | tail -1 | awk '{print $1}') #count the size of the longest line in all files
   awk -v LEN=$LEN '{if (length()==LEN) print "Output:'\''-: ", FNR, LEN, $0, "'\''";}' /tmp/stdin.txt #print all lines equal to the LEN
   rm /tmp/stdin.txt #remove the temporary helping file

else #if there are some arguments
   for argument in $@; do #check all of the arguments
      if [ $argument == '-h' ]; then #if the client ask for help
         echo -e "\n                         ZADANIE02    (C)ANASTASIIA-SOLOMIIA HRYTSYNA\n"
         echo "Usage:    bash z2.sh [OPTION]... <FILE>..."
         echo "Find the longest line in FILE(s) and print it (all of them) to the standard output in format:"
         echo -e "FileName: LineNumber, LineLength, Line\n"
         echo "FILE:"
         echo "       txt file(s) for analysing"
         echo "       With no FILE(s) - read standard input"
         echo "OPTION:"
         echo -e "    -h    display this help\n"
         exit 0
      fi
   done

   for argument in $@; do
      if [ -f $argument ]; then #if the argument is an existing file
         fileType=$(file $argument | awk '{print $2}') #get its type 
         if [ $fileType == 'ASCII' ] || [ $fileType == 'empty' ]; then #if it is a txt file or empty at all
            fileNames="$fileNames $argument" #append the file path to the fileNames
         else
            echo "Error: '$argument': does not have a text type." 1>&2
            #exit 1
         fi
      else #if the argument is not a file (or doesn't exist)
         echo "Error: '$argument' : does not exist or it is not a file." 1>&2
         #exit 1
      fi
   done

   if [ ! -z "$fileNames" ]; then
      LEN=$(wc -L $fileNames | tail -1 | awk '{print $1}') #count the size of the longest line in all files
      awk -v LEN=$LEN '{if (length()==LEN) print "Output: '\''", FILENAME, ":", FNR, LEN, $0, "'\''";}' $fileNames #print all lines equal to the LEN
   fi
fi

exit 0
