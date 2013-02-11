# shortcut script used to build the mapfish JS files
# It requires the jsbuild binary, a tool from the python jstools suite
# See http://github.com/whitmo/jstools

# build without removing comments and spaces:
jsbuild -u app.cfg

# build and minify
#jsbuild app.cfg


# TODO: build CSS
