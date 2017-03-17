#!/bin/sh

FILENAME="$1"

while read -r line
do
    echo $line
    curl -v \
        --data-urlencode "data=$line" \
        --trace-ascii out.txt \
        'http://localhost:9000/api/transactions/'
done < $FILENAME
