# shortcut script used to build the mapfish JS files
# It requires the jsbuild binary, a tool from the python jstools suite
# See http://github.com/whitmo/jstools

# build without removing comments and spaces:
#jsbuild -u c2corg.cfg

# build and minify
jsbuild c2corg.cfg
