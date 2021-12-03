#!/usr/bin/env bash

# change colors for gfl-bern.ch
# apply the script via customizer (copy it manually into the additional css section)

primary='#84B414/#19653F'
primary_dark='#516F0A/#004321'
primary_light='#CEE1A1/#C0D3BF'
primary_super_light='#E6F0D0/#DBE8DB'

secondary='#E10078/#F64662'
secondary_light='#F0CEE1/#FFCDD5'
secondary_dark='#B1025F/#BD1D3F'

text='#333333/#002A15'
black='#000000/#002A16'

echo "/** @see scripts/gfl.sh **/"

cat $(dirname $(realpath $0))/../wordpress/wp-content/themes/les-verts/styleguide/dist/static/style.min.css | \
	sed "s/$primary/gI; s/$primary_dark/gI; s/$primary_light/gI; s/$primary_super_light/gI;" | \
	sed "s/$secondary/gI; s/$secondary_light/gI; s/$secondary_dark/gI;" | \
	sed "s/$text/gI; s/$black/gI;"

echo ".m-branding{background-color:#00542A}"
