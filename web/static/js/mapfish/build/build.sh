# Temporary patch for OpenLayers (http://trac.openlayers.org/ticket/2328)
cd ../mfbase/openlayers
patch -N -p0 < ../../patches/patch-2328-r-9936-A0.diff

cd ../../build


# shortcut script used to build the mapfish JS files
# It requires the jsbuild binary, a tool from the python jstools suite
# See http://github.com/whitmo/jstools
jsbuild c2corg.cfg
