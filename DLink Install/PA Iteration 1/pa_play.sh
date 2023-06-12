#!/bin/bash
set -ex
shopt -s nullglob #Do not return search path if glob does not return files.

#############################
# Continuously generates playlist for EZStream 
# Author: Amie Davis
# Date: 7/13/16
#############################

#sox /home/pager/play_incoming/incoming.wav -c 1 -r 8000 /home/pager/play_queue/test.mp3
#ls -ltr #Get files descending


#Variables that can be changed.
####################
#PLAY_QUEUE_SEARCH=/home/pager/play_queue/*.mp3
PLAY_QUEUE_PATH=/home/pager/play_queue/ #Dont forget trailing slash
PLAY_FINISHED_PATH=/home/pager/play_finished/ #Don't forget trailing slash
PLAY_QUEUE_SEARCHPATH=/home/pager/play_queue/
INPUT_EXTENSION=.wav # Type to convert from.
OUTPUT_EXTENSION=.mp3 # Type to convert to and play.

PLAYLIST_FILE=/home/pager/playlist.m3u
CONTINUE_FILE=/home/pager/continuefile

PA_SERVERS[0]="172.16.34.1"
####################
#End of changeable variables. 

#init variables
FILES_TO_PLAY=""
FILES_TO_PLAY_COUNT="0"

#DESTINATION_FILE=""

#INCOMING_FILE_PATH="$INCOMING_FILE_PATH_DEFAULT"
#INCOMING_FILE_EXTENSION=".wav" #default

#INCOMING_FILENAME = $(basename "$INCOMING_FILE_PATH") #Basename
#INCOMING_FILE_EXT = "${INCOMING_FILENAME##*.}" #Extension only
#INCOMING_FILE = "${INCOMING_FILENAME%.*}" #Name without extension

#Check for any input files we need to convert.
PLAY_QUEUE_SEARCH_PATH=$PLAY_QUEUE_PATH*$INPUT_EXTENSION
PLAY_QUEUE_FILES_TOCONVERT=( $PLAY_QUEUE_SEARCH_PATH ) #Glob find files.

if [ ${#PLAY_QUEUE_FILES_TOCONVERT[@]} -gt 0 ]
then
	#First convert any wav files we have. 
#	FILES_TO_CONVERT=( $(ls -tr $PLAY_QUEUE_PATH*$INPUT_EXTENSION) )
	echo ${PLAY_QUEUE_FILES_TOCONVERT[@]}
	for INFILE in ${PLAY_QUEUE_FILES_TOCONVERT[@]}
	do
		OLDPATH=${INFILE}
		NEWPATH=$(basename "$OLDPATH")
		NEWPATH="${PLAY_QUEUE_PATH}${NEWPATH%.*}${OUTPUT_EXTENSION}"

		sox ${OLDPATH} -c 1 -r 8000 ${NEWPATH} #echotest
		rm ${OLDPATH}
	done
fi


#DESTINATION_FILE="${PLAY_QUEUE_FINISHED}${DESTINATION_FILE_NUMBER}${OUTPUT_EXTENSION}"

####

#Play file and move to finished folder.
FILES_TO_PLAY_PATH="$PLAY_QUEUE_PATH*$OUTPUT_EXTENSION"
FILES_TO_PLAY_ARRAY=( $FILES_TO_PLAY_PATH )
#FILES_TO_PLAY=( $FILES_TO_PLAY_PATH )
#FILES_TO_PLAY_COUNT=${#FILES_TO_PLAY[@]}

setupPAs() {
	COUNTER="0"
	while [ $COUNTER -lt ${#PA_SERVERS[@]} ]
	do
		mpc -h ${PA_SERVERS[$COUNTER]} -p 6600 clear 
		mpc -h ${PA_SERVERS[$COUNTER]} -p 6600 load pager 

		COUNTER="$(($COUNTER + 1))"
	done
}

startPAs() {
	COUNTER="0"
        while [ $COUNTER -lt ${#PA_SERVERS[@]} ]
        do
		mpc -h ${PA_SERVERS[$COUNTER]} -p 6600 play 1 &
		COUNTER="$(($COUNTER + 1))"
	done
}

stopPAs() {
	COUNTER="0"
        while [ $COUNTER -lt ${#PA_SERVERS[@]} ]
        do
                mpc -h ${PA_SERVERS[$COUNTER]} -p 6600 play 1 &
                COUNTER="$(($COUNTER + 1))"
        done
}

if [ ${#FILES_TO_PLAY_ARRAY[@]} -gt 0 ]
then
	FILES_TO_PLAY=( $(ls -tr $PLAY_QUEUE_PATH*$OUTPUT_EXTENSION) ) #Get files in time order.
	
	#Create Playlist for EZStream
	echo "#EXTM3U" > $PLAYLIST_FILE
	COUNTER="0"
	while [ $COUNTER -lt ${#FILES_TO_PLAY_ARRAY[@]} ]
	do
		OUTFILEPATH="${FILES_TO_PLAY[ $COUNTER ]}"
		echo "#EXTINF:-1,Fuck You PA" >> $PLAYLIST_FILE
		echo "$OUTFILEPATH" >> $PLAYLIST_FILE 
		COUNTER="$(($COUNTER + 1))"
	done

	#Prepare PAs to be activated
 	setupPAs
	#Start Playing Files via EZStream
	ezstream -c ezstream_mp3.xml & 
	EZSTREAM_PID=$!
	#Activate PAs
	startPAs

	wait #Wait for EZStream to finish playing before moving on.
	sleep 10s

	####
	#Check finished files so we know what to do when we are done playing.
	#These should never be deleted, so they are safe from conflicting filenames.
	PLAY_QUEUE_FINISHED_SEARCH_PATH=$PLAY_FINISHED_PATH*$OUTPUT_EXTENSION
	PLAY_QUEUE_FINISHED_FILES=( $PLAY_QUEUE_FINISHED_SEARCH_PATH )

	if [ ${#PLAY_QUEUE_FINISHED_FILES[@]} -gt 0  ]
	then
		DESTINATION_FILE_NUMBER="${#PLAY_QUEUE_FINISHED_FILES[@]}"
	else
		DESTINATION_FILE_NUMBER="0"
	fi

	#Move used files somewhere else.
	COUNTER="0"
        while [ $COUNTER -lt ${#FILES_TO_PLAY_ARRAY[@]} ]
        do
		DESTINATION_FILE_NUMBER="$(($DESTINATION_FILE_NUMBER + 1))"
		DESTINATION_FILE="${PLAY_QUEUE_FINISHED}${DESTINATION_FILE_NUMBER}${OUTPUT_EXTENSION}"
		mv ${FILES_TO_PLAY[ $COUNTER ]} $DESTINATION_FILE
		COUNTER="$(($COUNTER + 1))"
        done

	stopPAs #Stop PAs so they aren't just playing noise. 
fi


#If filename argument set.
#if [ $# -gt 0]
#then
#	INCOMING_FILE_PATH=$1#
#	INCOMING_FILENAME=$(basename "$INCOMING_FILE_PATH") #Basename
#	INCOMING_FILE_EXTENSION="${INCOMING_FILENAME##*.}" #Extension only
#fi


#If file exists and is bigger than 0.
#if [ -e $INCOMING_FILE_PATH -a -s $INCOMING_FILE_PATH ]
#then
#	createFilename
#
	#Make sure file doesn't exist.
#	while [ -e $DESTINATION_FILE ]
#	do
#		DESTINATION_FILE_NUMBER="$(($DESTINATION_FILE_NUMBER + 1))"
#		createFilename
#	done

#	cp $INCOMING_FILE_PATH $DESTINATION_FILE
#fi
