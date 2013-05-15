# shortcut script used to build the amp JS files
# It requires the jsbuild binary, a tool from the python jstools suite
# See http://github.com/whitmo/jstools

# Create c2c map i18n files
mkdir tmp
for culture in fr it de en es ca eu
do
  php mapi18n.php $culture > tmp/map-i18n-$culture.js
done

# Build and minify js files with jsbuild
# Use -u to build without removing comments and spaces
jsbuild app.cfg

# clear c2c map i18n files
rm -r tmp/

# TODO: build CSS
