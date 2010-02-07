# remove files from teh driectory that are more than one day old
find ../web/uploads/images_temp -type f -mtime +1 -exec rm -f {} \;
