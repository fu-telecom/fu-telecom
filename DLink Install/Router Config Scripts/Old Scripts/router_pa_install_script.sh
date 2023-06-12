#Setup packages
opkg update
opkg install kmod-usb-audio
opkg install kmod-sound-core
opkg install mpd
opkg install alsautils

#Setup MPD
echo "music_directory        \"~/\"" > /etc/mpd.conf
echo "playlist_directory \"/var/mpd/playlists\"" >> /etc/mpd.conf
echo "db_file \"/var/mpd/database\"" >> /etc/mpd.conf
echo "pid_file \"/var/run/mpd.pid\"" >> /etc/mpd.conf
echo "user \"root\"" >> /etc/mpd.conf
echo "group \"root\"" >> /etc/mpd.conf
echo "bind_to_address \"172.16.34.1\"" >> /etc/mpd.conf
echo "port \"6600\"" >> /etc/mpd.conf
echo "default_permissions             \"read,add,control,admin\"" >> /etc/mpd.conf
echo "input {" >> /etc/mpd.conf
echo "    plugin \"curl\"" >> /etc/mpd.conf
echo "}" >> /etc/mpd.conf
echo "audio_output {" >> /etc/mpd.conf
echo "        type            \"alsa\"" >> /etc/mpd.conf
echo "        name            \"My ALSA Device\"" >> /etc/mpd.conf
echo "         device          \"hw:0,0\"        # optional" >> /etc/mpd.conf
echo "         format          \"8000:24:1\"" >> /etc/mpd.conf
echo "         mixer_device    \"default\"       # optiona" >> /etc/mpd.conf
echo "         mixer_control   \"Speaker\"" >> /etc/mpd.conf
echo " }" >> /etc/mpd.conf

mkdir /var/mpd
mkdir /var/mpd/playlists

echo "#EXTM3U" > /var/mpd/playlists/pager.m3u
echo "#EXTINF:-1,Fuck Your Playlist" >> /var/mpd/playlists/pager.m3u
echo "http://172.16.1.2:8010/stream.mp3" >> /var/mpd/playlists/pager.m3u

echo "#!/bin/sh /etc/rc.common" > /etc/init.d/mpd
echo "# Copyright (C) 2007-2011 OpenWrt.org" >> /etc/init.d/mpd
echo "" >> /etc/init.d/mpd
echo "START=93" >> /etc/init.d/mpd
echo "" >> /etc/init.d/mpd
echo "start() {" >> /etc/init.d/mpd
echo "        service_start /usr/bin/mpd" >> /etc/init.d/mpd
echo "}" >> /etc/init.d/mpd
echo "" >> /etc/init.d/mpd
echo "stop() {" >> /etc/init.d/mpd
echo "        service_stop /usr/bin/mpd" >> /etc/init.d/mpd
echo "}" >> /etc/init.d/mpd

/etc/init.d/mpd enable
/etc/init.d/mpd start
