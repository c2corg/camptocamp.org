/*
 * @requires base.js
 */

c2corg = {};
c2corg.Map = function(config) {
    if (!this.initMap) {

        c2corg.base.init("fr"); // FIXME
        OpenLayers.inherit(c2corg.Map, cgxp.api.Map);

        c2corg.Map.prototype.initMap = function() {
            var mapConfig = c2corg.base.map;
            var config = this.adaptConfigForViewer(mapConfig);
            var viewer = new gxp.Viewer({
                portalConfig: {
                    renderTo: config.div,
                    height: Ext.get(config.div).getHeight(),
                    layout: "fit",
                    items: [config.id]
                },
                tools: [{
                    ptype: "c2corg_layertree",
                    outputConfig: {
                        closable: false,
                        title: OpenLayers.i18n("Camptocamp Objects"),
                        collapsible: true,
                        header: true,
                        width: 250
                    },
                    url: c2corg.config.mapserverUrl
                },
                {
                    ptype: "cgxp_mapopacityslider",
                    orthoRef: null,
                    actionTarget: "map.tbar",
                    defaultBaseLayerRef: "google_terrain"
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
                /*},
                {
                    ptype: "c2corg_georef",
                    actionTarget: "map.tbar",
                    toggleGroup: "maptools",
                    callback: function(lonlat) {
                        document.getElementById('lon').value = lonlat.lon;
                        document.getElementById('lat').value = lonlat.lat;
                    }
                    */
                }],
                sources: c2corg.base.sources,
                map: config
            });

            viewer.on('ready', this.onViewerReady.createDelegate(this, [viewer]));
        };

        return new c2corg.Map(config);
    }

    this.wmsURL = c2corg.config.mapserverUrl;
    return cgxp.api.Map.call(this, config);
};
