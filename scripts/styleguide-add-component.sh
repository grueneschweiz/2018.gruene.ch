#!/usr/bin/env bash

# usage: bash styleguide-add-compoenent.sh TYPE COMPONENT_NAME
TYPE=$1
COMPONENT_NAME=$2

FOLDER_PATH="styleguide/src/components/${TYPE}/${COMPONENT_NAME}"
SCSS="${FOLDER_PATH}/_${COMPONENT_NAME}.scss"
YML="${FOLDER_PATH}/${COMPONENT_NAME}.config.yml"
HBS="${FOLDER_PATH}/${COMPONENT_NAME}.hbs"

# make folder
mkdir ${FOLDER_PATH}
if [[ $? != 0 ]]
then
  echo "ERROR: ${FOLDER_PATH} could not be created. No files were created or modified."
  exit
fi


# bootstrap scss file
echo ".${COMPONENT_NAME} {}" > ${SCSS}

# create yml and hbs
touch ${YML}
touch ${HBS}

# done
echo "SUCCESS: ${COMPONENT_NAME} created"
echo "Don't forget to run 'yarn fractal sass:generate'"
