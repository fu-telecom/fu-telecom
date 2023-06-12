#!/bin/bash
set -ex

#############################
# Moves files into playing queue for PA system. 
# Author: Amie Davis
# Date: 7/13/16
#############################

#sox /home/pager/play_incoming/incoming.wav -c 1 -r 8000 /home/pager/play_queue/test.mp3

#Variables that can be changed.
####################
INCOMING_FILE_PATH_DEFAULT="/home/pager/play_incoming/incoming.wav"
DESTINATION_DIR="/home/pager/play_queue/"
####################
#End of changeable variables. 

DESTINATION_FILE_NUMBER="$(ls -1 /home/pager/play_queue | wc -l)"
DESTINATION_FILE=""

INCOMING_FILE_PATH="$INCOMING_FILE_PATH_DEFAULT"
INCOMING_FILE_EXTENSION=".wav" #default

#INCOMING_FILENAME = $(basename "$INCOMING_FILE_PATH") #Basename
#INCOMING_FILE_EXT = "${INCOMING_FILENAME##*.}" #Extension only
#INCOMING_FILE = "${INCOMING_FILENAME%.*}" #Name without extension


createFilename()
{
	DESTINATION_FILE="${DESTINATION_DIR}${DESTINATION_FILE_NUMBER}${INCOMING_FILE_EXTENSION}"
}

#If filename argument set.
if [ $# -gt 0 ]
then
	INCOMING_FILE_PATH=$1
	INCOMING_FILENAME=$(basename "$INCOMING_FILE_PATH") #Basename
	INCOMING_FILE_EXTENSION=".${INCOMING_FILENAME##*.}" #Extension only
fi


#If file exists and is bigger than 0.
if [ -e $INCOMING_FILE_PATH -a -s $INCOMING_FILE_PATH ]
then
	createFilename

	#Make sure file doesn't exist.
	while [ -e $DESTINATION_FILE ]
	do
		DESTINATION_FILE_NUMBER="$(($DESTINATION_FILE_NUMBER + 1))"
		createFilename
	done

	cp $INCOMING_FILE_PATH $DESTINATION_FILE
fi
