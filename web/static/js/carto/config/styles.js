/*
 * @requires config.js
 */

/**
 * See http://docs.openlayers.org/library/feature_styling.html
 * This script configures how the features are styled on the map
 */

Ext.namespace("c2corg");

c2corg.styleMap = function (config) {

    config = config || {};

    // define the different styles for c2c layers items
    var points = Ext.applyIf(config.points || {}, {
        pointRadius: 8,
        graphicOpacity: 1,
        externalGraphic: c2corg.config.staticBaseUrl + "/static/images/${icon}"
    }),
    lines = Ext.applyIf(config.lines || {}, {
        strokeColor: "yellow",
        strokeWidth: 2
    }),
    polygons = Ext.applyIf(config.polygons || {}, {
        strokeColor: "yellow",
        strokeWidth: 2,
        fillOpacity: 0
    }),
    pointsHover = Ext.applyIf({
        graphicOpacity: 0.6
    }, points),
    linesHover = Ext.applyIf({
        strokeColor: "red",
        strokeWidth: 3
    }, lines),
    polygonsHover = Ext.applyIf({
        strokeColor: "red",
        strokeWidth: 3,
        fillColor: "red",
        fillOpacity: 0.5
    }, polygons);

    // display a label when hovering point features in embedded map
    var labelsHover = Ext.applyIf(config.labelsHover || {}, {
        graphicOpacity: 1,
        label: "${name}",
        fontColor: "#f93",
        fontFamily: "sans-serif",
        fontWeight: "bold",
        fontSize: "10px",
        labelOutlineColor: "#fff",
        labelOutlineWidth: 3,
        labelYOffset: 18
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
            } else if (attr.module == "parkings" || attr.module == "public_transportations") {
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
    };

    var styleMap = new OpenLayers.StyleMap({
        cursor: 'pointer'
    });

    // style lookups for c2c layers
    var lookup = {
        "summits": points,
        "parkings": points,
        "public_transportations": points,
        "huts": points,
        "sites": points,
        "users": points,
        "images": points,
        "products": points,
        "routes": lines,
        "outings": lines,
        "maps": polygons,
        "areas": polygons, // so that areas can be displayed as map features
        "ranges": polygons,
        "countries": polygons,
        "admin_limits": polygons
    },
    lookupHover = {
        "summits": pointsHover,
        "parkings": pointsHover,
        "public_transportations": pointsHover,
        "huts": pointsHover,
        "sites": pointsHover,
        "users": pointsHover,
        "images": pointsHover,
        "products": pointsHover,
        "routes": linesHover,
        "outings": linesHover,
        "maps": polygonsHover,
        "ranges": polygonsHover,
        "countries": polygonsHover,
        "admin_limits": polygonsHover
    };

    styleMap.addUniqueValueRules("default", "module", lookup, context);
    styleMap.addUniqueValueRules("temporary", "module", lookupHover, context);

    // features that have label=true property will have their label
    // displayed on the map
    var lookupLabel = {
        "true": labelsHover
    };
    styleMap.addUniqueValueRules("temporary", "label", lookupLabel);

    return styleMap;
};
