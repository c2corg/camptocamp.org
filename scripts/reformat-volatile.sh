#!/bin/sh

DEV="/dev/volatile"

if [ $(id -u) != 0 ]; then
  echo "this script can only be run as root"
  exit 1
fi

MNT=$(grep $DEV /etc/mtab | cut -f2 -d' ')

if test -d $MNT; then
  DIRS=$(ls $MNT)

  (umount $DEV && mkfs.xfs -f $DEV && mount $DEV) || exit 1

  chown www-data. $MNT

  for dir in $DIRS; do
    mkdir -p $MNT/$dir
    chown www-data. $MNT/$dir
  done

fi
