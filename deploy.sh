#!/bin/bash

set -euo pipefail

# path to the file with the configurations
# the config must follow the following pattern
# name 		user@host			site path			composer path			wp cli path
siteconffile='deploy.conf'

##########################################
echoerr()
{
	printf "%s\n" "$*" >&2
}

getConfigs()
{
	local site="$1"

	if [ ! -r "$siteconffile" ]; then
		echoerr "ERROR: Unable to read config file '$siteconffile'"
	fi

	while read -r line
	do
		local config=( ${line} )

		if [ "5" -ne "${#config[@]}" ]; then
			echoerr "ERROR: Invalid config for '${config[0]}'."
			echoerr "5 values expected but ${#config[@]} found."
			exit 2
		fi

		if [[ "${site}" == "${config[0]}" ]]; then
			echo "${line}"
			exit 0
		fi

		if [[ "-n" == "${site}" ]]; then
			echo "${config[0]}"
		fi
	done <<< `sed '/^\s*#/ d; /^\s*$/ d;' $siteconffile`

	if [[ "-n" != "${site}" ]]; then
		echoerr "ERROR: no configuration for ${site}"
		exit 1
 	fi
}

clearcache()
{
	local host="$1"
	local target="$2"
	local wp="$3"

	if ssh "$host" [ ! -x "\"$wp\"" ]; then
		echoerr "ERROR: wp command on remote host not found or not executable."
		echoerr "Tried: $wp"
		exit 3
	fi

	# if multisite
	if ssh "$host" "\"$wp\" --path=\"${target}\" site list" > /dev/null 2>&1; then
		local sites=$( ssh "$host" "\"$wp\" --path=\"${target}\" site list --field=url" )

		for site in $sites; do
			echo "Clearing cache of ${site}"
			ssh "$host" "\"$wp\" --url=\"${site}\" --path=\"${target}\" super-cache flush"
		done

	# no multisite
	else
		ssh "$host" "\"$wp\" --path=\"${target}\" super-cache flush"
	fi
}

deploysingle()
{
	local name="$1"
	local host="$2"
	local target="$3"
	local composer="$4"
	local wp="$5"
	local plugins=$6
	local quiet=$7
	local skipassets=$8
	local flushcache=$9

	if [ -z "$host" ]; then
		echoerr "ERROR: Missing host argument";
	fi

	if [ -z "$target" ]; then
		echoerr "ERROR: Missing target argument";
	fi

	if [[ "0" == "$skipassets" ]]; then
		buildassets
	fi

	echo "Deploying $name"

	if [[ "0" == "$quiet" ]]; then
		sync "$host" "$target" $plugins $quiet

		read -p "The above files will be deployed for '$name'. Continue? [y/n] " -n 1
		echo

		if [[ ! "$REPLY" =~ ^[Yy]$ ]]; then
			return 1
		fi
	fi

	sync "$host" "$target" $plugins 1

	if [[ "0" != "$composer" ]]; then
		if ssh "$host" [ ! -x "\"$composer\"" ]; then
		 echoerr "ERROR: Composer command on remote host not found or not executable."
		 echoerr "Tried: $composer"
		 exit 3
	 	fi

		ssh "$host" "\"$composer\" --working-dir=\"${target}/wp-content/themes/les-verts\" install"
	fi

	if [[ "0" != "$flushcache" ]]; then
		clearcache "$host" "$target" "$wp"
	fi

	echo "Success: Deployed $name"
}

sync()
{
	local host="$1"
	local target="$2"
	local plugins=$3
	local quiet=$4
	local dry=

	if [[ "1" != "$quiet" ]]; then
		dry="n"
	fi

	rsync -vrz${dry} --exclude='vendor' --exclude='styleguide' --exclude='static' ./wordpress/wp-content/themes/les-verts "${host}:${target}/wp-content/themes/"
	rsync -vrz${dry} --exclude='styleguide' --exclude='img' ./wordpress/wp-content/themes/les-verts/styleguide/dist/static "${host}:${target}/wp-content/themes/les-verts/"

	if [[ "1" == "$plugins" ]]; then
		plugins=( "advanced-custom-fields-pro" "polylang-pro" "searchwp" "searchwp-polylang" )
		for plugin in "${plugins[@]}"
		do
			if [ -d "./wordpress/wp-content/plugins/${plugin}" ]; then
				rsync -vrz${dry} "./wordpress/wp-content/plugins/${plugin}" "${host}:${target}/wp-content/plugins/"
			else
				echoerr "ERROR: Plugin ${plugin} not found."
				exit 4
			fi
		done
	fi
}

buildassets() {
	yarn build
}

usage()
{
	echo "Usage: deploy [ -c ] [ -C ] [ -p ] [ -q ] [ -s ] -a | ( names )"
	echo "  -c	run composer"
	echo "  -C  clear wp super cache"
	echo "  -p	sync non-free plugins"
	echo "  -q	quiet"
	echo "  -s	skip building assets"
	echo "  -a 	deploy all sites in config. mutually exclusive with names"
	echo "  names	names of the sites to deploy. separate by a space. mutually exclusive with -a option."
	exit 2
}

##################### The program starts here

all=0
with_composer=0
clear_cache=0
with_plugins=0
quiet=0
skip=0
composer_path=0

while getopts 'acCpqs' opt
do
	case "$opt" in
		"a") all=1				;;
		"c") with_composer=1 	;;
    "C") clear_cache=1    ;;
		"p") with_plugins=1		;;
		"q") quiet=1			;;
		"s") skip=1				;;
		*) usage; exit 1	;;
	esac
done

if [[ "0" == "$all" ]]; then
	sites=( "${@:$OPTIND}" )
else
	sites=( `getConfigs -n` )
fi

if [ -z "$sites" ]; then
	echoerr "ERROR: Missing argument. Provide a list of of sites to update."
	usage
	exit 1
fi

for site in "${sites[@]}"
do
	config=`getConfigs ${site}`
	if [ "$?" -gt "0" ]; then
		exit 1
	fi

	config=( $config )

	if [[ "0" != "${with_composer}" ]]; then
		composer_path="${config[3]}"
	fi

	wp_path=
	if [[ "0" != "${clear_cache}" ]]; then
		wp_path="${config[4]}"
	fi

	deploysingle "${config[0]}" "${config[1]}" "${config[2]}" "${composer_path}" "${wp_path}" ${with_plugins} ${quiet} ${skip} ${clear_cache}
	echo -e "\n--\n\n"

	skip=1
done
