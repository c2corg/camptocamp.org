DIR=/srv/chroot-c2corg/var/www/camptocamp.org/cache
umount $DIR
mkfs.xfs -f /dev/md3
mount $DIR
chown 33:33 $DIR
chmod 2775 $DIR
