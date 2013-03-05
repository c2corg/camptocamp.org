/*
 * @requires config.js
 */

/**
 * See http://docs.openlayers.org/library/feature_styling.html
 */

Ext.namespace("c2corg");

c2corg.styleMap = function (config) {

    config = config || {};
    var points = Ext.applyIf(config.points || {}, {
        pointRadius: 8,
        graphicOpacity: 1,
        externalGraphic: c2corg.config.staticBaseUrl + "/static/images/${icon}"
    }),
        lines =  Ext.applyIf(config.lines || {}, {
            strokeColor: "yellow",
            strokeWidth: 2
        });

    /*
    // TODO: rename summits picto with names containing the "summit_type" attribute
    // or add an attribute that contains the name of the picto to use
    var summits = Ext.applyIf({
        externalGraphic: c2corg.config.staticBaseUrl + "/static/images/picto/summit_${summit_type}.png" 
    }, points);
    */
    
    var context = function (feature) {
        var attr = feature.attributes; 
        if (feature.geometry instanceof OpenLayers.Geometry.Point) {
            attr.icon = "modules/" + attr.module + "_mini.png";
            if (attr.module == "summits") {
                if (typeof attr.summit_type != "undefined") {
                    if (attr.summit_type == 2) attr.icon = "picto/pass.png";
                    if (attr.summit_type == 3) attr.icon = "picto/lake.png";
                    if (attr.summit_type == 4) attr.icon = "picto/crag.png";
                    // FIXME: other types?
                }
            } else if (attr.module == "parkings") {
                if (typeof attr.public_transportation_rating != "undefined") {
                    if (["1","2","4","5"].indexOf(attr.public_transportation_rating) != -1) {
                        attr.icon = "picto/parking_green.png";
                    }
                }
            } else if (attr.module == "huts") {
                if (typeof attr.shelter_type != "undefined") {
                    if (attr.shelter_type == 3) attr.icon = "picto/camp.png";
                    if (attr.shelter_type == 5) attr.icon = "picto/gite.png";
                }
            }
        }
        return attr;
    }

    var styleMap = new OpenLayers.StyleMap({
        cursor: 'pointer'
    });
    var lookup = {
        "summits": points,
        "parkings": points,
        "huts": points,
        "routes": lines,
        "outings": lines
    };
    styleMap.addUniqueValueRules("default", "module", lookup, context);
    return styleMap;
};
