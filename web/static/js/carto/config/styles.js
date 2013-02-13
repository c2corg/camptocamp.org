/*
 * @requires config.js
 */

/**
 * See http://docs.openlayers.org/library/feature_styling.html
 */

Ext.namespace("c2corg");

c2corg.styleMap = function(config) {

    config = config || {};    
    var points = Ext.applyIf(config.points || {}, {
        pointRadius: 8,
        graphicOpacity: 1,
        externalGraphic: c2corg.config.staticBaseUrl + "/static/images/modules/${module}_mini.png"
    });
    var lines =  Ext.applyIf(config.lines || {}, {
        strokeColor: "yellow",
        strokeWidth: 2
    });

    var summits = Ext.applyIf({
        // TODO: rename summits picto with names containing the "summit_type" attribute
        // or add an attribute that contains the name of the picto to use
        //externalGraphic: c2corg.config.staticBaseUrl + "/static/images/picto/summit_${summit_type}.png" 
        externalGraphic: c2corg.config.staticBaseUrl + "/static/images/${summitIcon}"
    }, points);

    var context = function(feature) {
        var attr = feature.attributes; 
        attr.summitIcon = "modules/summits_mini.png";
        if (typeof attr.summit_type != "undefined") {
            if (attr.summit_type == 1) attr.summitIcon = "modules/summits_mini.png";
            if (attr.summit_type == 2) attr.summitIcon = "picto/pass.png";
            if (attr.summit_type == 3) attr.summitIcon = "picto/lake.png";
            if (attr.summit_type == 4) attr.summitIcon = "picto/crag.png";
            // FIXME: other types?
        }
        return attr;
    }

    var styleMap = new OpenLayers.StyleMap({
        cursor: 'pointer'
    });
    var lookup = {
        "summits": summits,
        "parkings": points,
        "huts": points,
        "routes": lines,
        "outings": lines
    };
    styleMap.addUniqueValueRules("default", "module", lookup, context);
    return styleMap;
};
