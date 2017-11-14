#!/bin/sh

FILENAME="$1"
count=0
count_with_200=0
count_with_201=0
count_with_warning=0

while read -r line
do
    output=$(curl \
        -w "%{http_code} %{url_effective}" \
        --data-urlencode "data=$line" \
        --silent \
        'http://localhost:9000/api/intesa/transactions/')
        # 'http://localhost:9000/api/transactions/')
    count=$((count+1))
    http_code=$(echo $output|cut -d ' ' -f1)
    if [ "$http_code" == "200" ]
    then
        echo $line
        echo $output
        count_with_200=$((count_with_200+1))
    elif [ "$http_code" == "201" ]
    then
        echo -n "."
        count_with_201=$((count_with_201+1))
    else
        echo "WARNING:"
        echo $line
        echo $output
        count_with_warning=$((count_with_warning+1))
    fi
done < $FILENAME
echo ""
echo "Total POST executed: $count"
echo " - with response 201: $count_with_201"
echo " - with response 200: $count_with_200"
echo " - with response WARNING: $count_with_warning"
