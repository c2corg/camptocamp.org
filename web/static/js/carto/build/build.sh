# shortcut script used to build the amp JS files
# It requires the jsbuild binary, a tool from the python jstools suite
# See http://github.com/whitmo/jstools

# exit as soon as one command fails
set -e

# Create c2c map i18n files
mkdir -p tmp
for culture in fr it de en es ca eu
do
  php mapi18n.php $culture > tmp/map-i18n-$culture.js
done

# Remove existing files
# so to make sure that we don't keep old obsolete built js files
rm *.js

# Build and minify js files with jsbuild
# Use -u to build without removing comments and spaces
jsbuild app.cfg

# clear c2c map temp i18n files
rm -r tmp/
