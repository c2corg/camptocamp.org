# shortcut to build css file with sass

set -e # exit on error

# delete obsolete css files
for i in `ls ../css | grep '\.css$'`; do
  if [ ! -f ${i%css}scss ]; then
    rm -f ../css/$i
  fi
done

# compile stylesheets
compass compile
