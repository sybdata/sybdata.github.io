#!/bin/sh
# for *nix shell

get_file_zproxy()
{
 local URL="http://ip:7171/$(basename $1)"
 curl --compressed --connect-timeout 10 --max-time 30 --fail -R -z "$1" -o "$1" "${URL}"
}

get_file_zproxy /patch to/playlist_nd.m3u8

sed -i 's/0.0.0.0/ip/g' /patch to/playlist_nd.m3u8

mv /patch to/playlist_nd.m3u8 /patch to/hls5_zmp_nd.m3u8
