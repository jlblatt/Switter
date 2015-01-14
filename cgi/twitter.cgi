#!/bin/bash

#ARGS=$1
ARGS="$(perl -MURI::Escape -e 'print uri_escape($ARGV[0]);' "$1")"

RES=`curl http://search.twitter.com/search.json?q=$ARGS`

echo "Content-type: text/json"
echo ""
echo $RES

