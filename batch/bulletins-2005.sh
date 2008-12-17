#!/bin/sh

export LC_ALL="fr_FR.UTF-8"

WORKDIR="/var/vmail/camptocamp.org/meteofrance/work/"
TYPE="bulletinneige"
LIST="DEPT74 DEPT73 DEPT38 DEPT04 DEPT05 DEPT06 DEPT2A DEPT2B DEPT66 DEPT31 DEPT09 ANDORRE DEPT64 DEPT65"
TITLE="Bulletin Neige et Avalanches"

PATH="/usr/bin:/usr/sbin:/bin:/sbin"
unset http_proxy

TODAY=`LC_ALL="fr_FR.ISO-8859-1" date +"%d %b %y"`

# debug
NOW=`date +"%d%b%Y-%Hh%Mm%Ss"`

if [ ! -d $WORKDIR ] || [ ! -w $WORKDIR ]
then
    echo "$0: ERROR: $WORKDIR not accessible !"
    exit 0
fi

for DPT in $LIST; do

    URL="http://france.meteofrance.com/france/MONTAGNE?MONTAGNE_PORTLET.path=montagne$TYPE%252F$DPT"
    FILE=$WORKDIR/$DPT-$TYPE

    # test if server is up && file exists
    if w3m -dump_head -no-cookie $URL | egrep --quiet '200 OK$'; then

        # dump bulletin to a file
        curl -s $URL | grep "onlyText bulletinText" | perl -pe 's/.*bulletinText">(.+?)<\/div.*/$1/' | w3m -M -dump -T "text/html" -I "utf-8" -cols 74 > $FILE.txt
# debug
#        cp $FILE.txt $WORKDIR/debug/$DPT-$TYPE.$NOW.curl.txt

        echo -e "\n\n\n" >> $FILE.txt
        sed -i ':a;N;$!ba;s/\n\{2,\}/\n\n/g' $FILE.txt
# debug
#        cp $FILE.txt $WORKDIR/debug/$DPT-$TYPE.$NOW.sed.txt



        # files differ: send it by email
        if ! cmp --quiet $FILE.mail $FILE.txt; then
# debug
#            touch $WORKDIR/debug/$DPT-$TYPE.$NOW.mailsent

            MAILDPT=`echo $DPT | sed -e 's/^dept//i'`

            mv $FILE.txt $FILE.mail
            mail -a "Content-Type: text/plain; charset=utf-8" -s "$TITLE $DPT - $TODAY" meteofrance-$MAILDPT@camptocamp.org < $FILE.mail

        else
            rm -f $FILE.txt
        fi
    else
        # problem with server
        logger "bulletin meteofrance: unable to download $DPT ($URL)"
        # false
    fi

done
