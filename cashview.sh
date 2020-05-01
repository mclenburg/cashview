#!/bin/sh
mysql -h 192.168.5.103 -u cashview -p cashview --password=cash123 <<EOF
insert into transaktionen(Wert, KtoID, katID, Datum) (select Wert, KtoID, katID, now() from laufendes where mod(MONTH(now()), modulo)=0);
commit;
exit
EOF


