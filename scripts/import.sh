#!/bin/bash

set -e

usage()
{
	echo "This command will import posts, pages and attachments of the given sites (site_slugs)."
	echo ""
	echo "Usage: import [ -h ] network_url site_slugs..."
	echo "  -h           This screen"
	echo "  network_url  The base url of the WPMU network. With trailing slash!"
	echo "  site_slugs   The slugs of the site where the content should be imported"
	echo ""
	echo "The files with the content to import must be placed in ./import and stick to the"
	echo 'following naming convention: ${site_slug}.${post_type}.xml'
	echo "Example: ./import/lausanne.attachment.xml"
	echo ""
	echo "The command must be executed from the WordPress' base directory."
	exit 2
}

import_attachments() {
	local file="$1" # wxr import file
	local url="$2"  # site url (multisite)

	for i in {1..10}
			do
				echo "importing attachments. attempt $i"
				if wp --url="$url" import --authors=skip "$file"; then
						echo "SUCCESS: imported all attachments"
						i=0
						break
				fi
			done

			if [ $i -ge 10 ]; then
				echo "ERROR importing attachments"
				exit 1
			fi
}

import_post_type() {
	local post_type="$1"
	local file="$2" # wxr import file
	local url="$3" # site url (multisite)

	if [ ! -f "$file" ]; then
		echo "ERROR: Import file could not be found. Looking for: $file"
		echo "Call $0 -h for more information"
		exit 1
	fi

	if [[ $post_type == "attachment" ]]; then
			import_attachments "$file" "$url"
		else
			wp --url="$url" import --authors=skip "$file"
		fi
}


migrate_post_content() {
		local url="$1"
		if wp --url="$url" eval-file "wp-content/themes/les-verts/lib/admin/import-handler.php"; then
			echo "Migration completed"
		else
			echo "ERROR migrating content."
			exit 1
		fi
}


while getopts 'h' opt
do
	case "$opt" in
		"h") usage; exit 1 ;;
		*) usage; exit 1	;;
	esac
done

if [ -z "$1" ] || [ -z "$2" ]; then
	usage
	exit 1
fi

base_url="$1"
shift 1
sites=( "$@" )

post_types=(post page attachment)

for site in ${sites[*]}
do
	echo
	echo "Start importing $site..."

	url="${base_url}${site}"

	for post_type in ${post_types[*]}
	do
		file="./import/${site}.${post_type}.xml"
		echo "Importing $post_type for $site from $file..."
		import_post_type "$post_type" "$file" "$url"
	done

	echo "Migrating post content..."
	migrate_post_content "$url"
done

echo "SUCCESS. All done. Don't forget to remove the import files!"

