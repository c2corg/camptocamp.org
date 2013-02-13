/*
 * @requires base.js
 */

Ext.namespace("c2corg");

c2corg.Map = function(config) {

    config = Ext.applyIf(config, {
        id: config.div + "-map",
        lang: "fr",
        layers: [],
        features: null,
        basemap: "google_terrain"
    });

    // Set OpenLayers/GeoExt params + lang
    c2corg.base.init(config.lang);

    var mapConfig = c2corg.base.map;
    mapConfig.id = config.id;
    
    var tools = [{ 
        ptype: "c2corg_layertree",
        outputConfig: {
            closable: false,
            title: OpenLayers.i18n("c2c data"),
            collapsible: true,
            header: true,
            width: 250
        },
        initialThemes: config.layers, 
        url: c2corg.config.mapserverUrl
    },
    {
        ptype: "cgxp_mapopacityslider",
        orthoRef: null,
        actionTarget: "map.tbar",
        defaultBaseLayerRef: config.basemap
    },
    {
        ptype: "cgxp_zoom",
        actionTarget: "map.tbar",
        toggleGroup: "maptools"
    },
    {
        ptype: "gxp_navigationhistory",
        actionTarget: "map.tbar"
    },
    {
        ptype: "cgxp_permalink",
        actionTarget: "map.tbar"
    },
    {
        ptype: "cgxp_measure",
        actionTarget: "map.tbar",
        toggleGroup: "maptools"
    },
    /*{
        ptype: "c2corg_georef",
        actionTarget: "map.tbar",
        toggleGroup: "maptools",
        callback: function(lonlat) {
            document.getElementById('lon').value = lonlat.lon;
            document.getElementById('lat').value = lonlat.lat;
        }
    },*/
    {
        ptype: "c2corg_showfeatures",
        features: config.features
    }];
    
    viewer = new gxp.Viewer({
        portalConfig: {
            renderTo: config.div,
            height: Ext.get(config.div).getHeight(),
            layout: "fit",
            items: [config.id]
        },
        tools: tools,
        sources: c2corg.base.sources,
        map: mapConfig
    });

    viewer.on('ready', function() {
        
        // remove loading message if any
        if (config.loading) {
            Ext.fly(config.loading).fadeOut({
                remove: true
            });
        }

        if (config.center) {
            var map = this.mapPanel.map, center = config.center;
            var lonlat = new OpenLayers.LonLat(center[0], center[1]);
            lonlat = lonlat.transform("EPSG:4326", map.getProjection());
            map.setCenter(lonlat, center[2]); 
        }
    
        // FIXME: resize event is not detected
        viewer.portal.body.on('resize', function() {
            viewer.portal.doLayout();
        });
    }, viewer, config);
};
