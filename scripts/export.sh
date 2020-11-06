#!/bin/bash

set -e

mkdir export

urls=( $(wp site list --field=url) )
domains=( $( wp site list --field=domain ) )
paths=( $( wp site list --field=path | sed 's/\///g' ) )
post_types="post page attachment"

for i in "${!domains[@]}"
do
	site="${domains[$i]}_${paths[$i]}"
	url="${urls[i]}"
	for type in ${post_types[*]}
	do
		wp --url="$url" export --post_type="$type" --dir="./export" --filename_format="${site}.${type}.xml"
	done
done

echo "SUCCESS: exported all ${post_types[*]} into ./export"
