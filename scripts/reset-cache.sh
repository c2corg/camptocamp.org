DIR=/var/lib/vz/private/1028/var/www/camptocamp.org/cache
umount $DIR
mkfs.xfs -f /dev/mapper/vg0-sfcache
mount $DIR
chown 33:33 $DIR
chmod 2775 $DIR
