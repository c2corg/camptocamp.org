Ext.namespace("c2corg");

// TODO: call lib only when needed
document.write('<script type="text/javascript" src="http://api.ign.fr/api?v=1.0beta4-m&key=');
document.write(c2corg.config.gpKey + '&includeEngine=false"></script>');
document.write('<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=');
document.write(c2corg.config.gmKey + '"></script>');

Proj4js.defs["EPSG:900913"]= "+title= Google Mercator EPSG:900913 +proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +no_defs";

c2corg.API = OpenLayers.Class(MapFish.API, {

    lang: 'fr',
    provider: null,
    //includedProviderLibs: null,

    epsg4326: new OpenLayers.Projection("EPSG:4326"),
    miller: new OpenLayers.Projection("IGNF:MILLER"),
    fxx: new OpenLayers.Projection("IGNF:GEOPORTALFXX"),
    epsg900913: new OpenLayers.Projection("EPSG:900913"),

    initialize: function(config) {
        config = config || {};
        MapFish.API.prototype.initialize.apply(this, arguments);

        this.baseConfig = c2corg.config;
        if (config) {
            Ext.apply(this.baseConfig, config);
        }

        this.provider = config.provider || 'ign';

        Ext.BLANK_IMAGE_URL = this.baseConfig.baseUrl + '/static/js/mapfish/mfbase/ext/resources/images/default/s.gif';
		OpenLayers.ImgPath = this.baseConfig.baseUrl + '/static/images/openlayers/';
    },

    /* public methods */
/*
    createMap: function(config) {
	    this.includeProviderLib(this.provider);
	    MapFish.API.prototype.createMap.apply(this, arguments);
    },
*/

    createToolbar: function(config) {
	
        config = config || {};
        var items = [], action;

        // zoom to initial extent
        items.push(new GeoExt.Action(Ext.apply({
            map: this.map,
            control: new MapFish.API.ZoomToExtent(config.controls),
            iconCls: 'zoomfull'
            //toggleGroup: 'navigation',
            //allowDepress: false,
            //text: "max extent"
        }, config.actions)));

        // pan mode
        items.push(new Ext.Button(Ext.apply({
            toggleGroup: 'navigation',
            allowDepress: false,
            pressed: true,
            //text: 'nav',
            iconCls: 'pan'
        }, config.actions)));

        // zoom box mode
        items.push(new GeoExt.Action(Ext.apply({
            map: this.map,
            control: new OpenLayers.Control.ZoomBox(config.controls),
            toggleGroup: 'navigation',
            allowDepress: false,
            //text: 'zoom box',
            iconCls: 'zoomin'
        }, config.actions)));

        // length measure
        var measure = new MapFish.API.Measure(config.controls);
        items.push(new GeoExt.Action(Ext.apply({
            map: this.map,
            control: measure.createLengthMeasureControl(),
            toggleGroup: 'navigation',
            allowDepress: false,
            //text: 'length',
            iconCls: 'measureLength'
        }, config.actions)));

        // navigation history
        var history = new OpenLayers.Control.NavigationHistory(config.controls);
        history.activate();
        this.map.addControl(history);

        items.push(new GeoExt.Action(Ext.apply({
            tooltip: OpenLayers.i18n("previous"),
            control: history.previous,
            iconCls: 'previous',
            disabled: true
        }, config.actions)));

        items.push(new GeoExt.Action(Ext.apply({
            tooltip: OpenLayers.i18n("next"),
            control: history.next,
            iconCls: 'next',
            disabled: true
        }, config.actions)));

/*
        items.push({
	        xtype: 'combo',
	        mode: 'local',
	        forceSelection: true,
	        readOnly: true,
	        editable: false,
	        value: OpenLayers.i18n('IGN'), // TODO: get from this.provider
	        triggerAction: 'all',
		    store: [
		        ['gmap', 'Google Maps'],
		        ['ign', 'IGN'],
		        ['osm', 'OpenStreetMap']
		    ],
		    valueField: 'providerId',
		    displayField: 'providerName',
		    listeners: {
			    select: function(combo, record, index) {
				    if (record.data.value == this.provider) return;
				    this.provider = record.data.value;
				    this.switchProvider();
				},
				scope: this
		    }
        });
*/

        if (this.isMainApp) {
	        items.push('->');
	
	        // permalink
	        this.initLinkPanel();
	        var permalink = new MapFish.API.Permalink('permalink', null, {api: this});
			permalink.activate();
            this.map.addControl(permalink);

			items.push(new Ext.Action({
			    text: OpenLayers.i18n('permalink'),
			    enableToggle: true,
			    handler: function() {
			        var lc = Ext.get('linkContainer');
			        if (!lc.isVisible()) {
			            lc.show();
			            this.linkPanel.enable();
			            this.linkPanel.doLayout();
			        } else {
			            lc.hide();
			            this.linkPanel.disable();
			        }
			    },
			    scope: this
			}));

	
	        // expand/reduce map
	        var map = this.map;
            items.push(new Ext.Button({
	            text: OpenLayers.i18n('Expand map'),
	            handler: function() {
		            var mapheader = Ext.getCmp('mapheader');
		            var mapfooter = Ext.getCmp('mapfooter');
		            var mappanel = Ext.getCmp('mappanel');
		            var sidepanel = Ext.getCmp('sidepanel');
		
		            if (mapheader.isVisible()) {
			            mapheader.hide();
			            mapfooter.hide();
			            if (!sidepanel.collapsed) {
			                sidepanel.collapse();
		                }
			            mappanel.doLayout();
			            map.updateSize();
				        this.setText(OpenLayers.i18n('Reduce map'));
		            } else {
			            mapheader.show();
			            mapfooter.show();
			            if (sidepanel.collapsed) {
			                sidepanel.expand();
		                }
			            mappanel.doLayout();
				        this.setText(OpenLayers.i18n('Expand map'));
		            }
	            }
            }));
        }
		
		/*items.push(new mapfish.widgets.LoadingIndicator({
		    text: OpenLayers.i18n('Loading...'),
		    map: this.map
		}));*/

        return items;
    },

    /* private methods */

    getMapOptions: function() {
	    switch (this.provider) {
		    case 'ign': return this.getIgnMapOptions();
		    default: return this.getGmapMapOptions();
	    }
	},
	
	getGmapMapOptions: function() {
	    return {
	        projection: "EPSG:900913",
	        units: "m",
	        maxResolution: 156543.0339,
	        maxExtent: new OpenLayers.Bounds(-20037508, -136554022,
	                                         20037508, 136554022)
	    };	
	},
	
	getIgnMapOptions: function() {
        return {
	        resolutions: Geoportal.Catalogue.RESOLUTIONS.slice(5,18), 
            projection: 'IGNF:GEOPORTALFXX',
            maxExtent: new OpenLayers.Bounds(-15, 34, 26, 58).transform(this.epsg4326, this.fxx, true), 
            units: this.fxx.getUnits()
        };
    },

    getControls: function(config) {
        var options = this.getMapOptions();
        var controls = [
            new OpenLayers.Control.PanZoomBar(),
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.MousePosition({
                div: $('mousepos'),
                numDigits: 6,
                prefix: OpenLayers.i18n('longitude / latitude: '),
                displayProjection: this.provider == 'gmap' ? this.epsg900913 : this.epsg4326
            }),
/*
            new OpenLayers.Control.OverviewMap({
                div: $('overviewmap'),
                layers: [
                    new OpenLayers.Layer.Image("overview",
                        this.baseConfig.baseUrl + "/gfx/keymap.png",
                        new OpenLayers.Bounds(485000, 65000, 835000, 298000),
                        new OpenLayers.Size(150, 99))
                ],
                size: new OpenLayers.Size(180, 100),
                isSuitableOverview: function() {return true;},
                mapOptions: {
                    units: options.units,
                    projection: options.projection,
                    maxExtent: options.maxExtent,
                    scales: [7000000]
                }
            }),
*/
            new OpenLayers.Control.Scale($('scale'), {
                updateScale: function() {
                    var scale = this.map.getScale();
                    if (!scale) {
                        return;
                    }
                    this.element.innerHTML = OpenLayers.i18n("scale", {
                        scaleDenom: OpenLayers.Number.format(scale, 0, "'")
                    });
                }
            })
        ];

        if (this.isMainApp) {
            controls.push(new MapFish.API.ArgParser({api: this}));
            // Permalink control is added later because it needs the toolbar to initialize
        }

        return controls;
    },
    
    getLayers: function(config) {
	    var provider = config.provider || this.provider;
	
	    var layers = [
	        new OpenLayers.Layer.WMS(
                "c2corg",
                this.baseConfig.wmsUrl, {
                    layers: ['summits', 'parkings'], 
                    format: 'image/png', 
                    transparent: true
                }, {
                    maxResolution: 2048,
                    numZoomLevels: 13,
                    singleTile: true,
                    projection:'EPSG:4326',
                    units: 'degrees',
                    visibility: false,
                    isBaseLayer: false
                }
            )
	    ];
	    
	    switch (provider) {
		    case 'ign': return layers.concat(this.getIgnLayers());
		    //case 'osm': return layers.concat(this.getOsmLayers());
		    default: return layers.concat(this.getGmapLayers());
	    }
	},
	
	getOsmLayers: function(config) {
		return [];
	},
	
	getGmapLayers: function(config) {
		return [
		    new OpenLayers.Layer.Google(
			    "gmap_physical", {
			        type: G_PHYSICAL_MAP,
			        sphericalMercator: true
			        //  google layer has 20 zoom levels (0 to 19) (from farther to closer)
			    },{
			        buffer: 0,
			        minZoomLevel: 0,
			        maxZoomLevel: 17
			        // we therefore limit the number of zoom levels to 18
			    }
		    ),
		    new OpenLayers.Layer.Google(
			    "gmap_hybrid", {
			        type: G_HYBRID_MAP,
			        sphericalMercator: true
			        //  google layer has 20 zoom levels (0 to 19) (from farther to closer)
			    },{
			        buffer: 0,
			        minZoomLevel: 0,
			        maxZoomLevel: 17
			        // we therefore limit the number of zoom levels to 18
			    }
		    ),
		    new OpenLayers.Layer.Google(
			    "gmap_satellite", {
			        type: G_SATELLITE_MAP,
			        sphericalMercator: true
			        //  google layer has 20 zoom levels (0 to 19) (from farther to closer)
			    },{
			        buffer: 0,
			        minZoomLevel: 0,
			        maxZoomLevel: 17
			        // we therefore limit the number of zoom levels to 18
			    }
		    ),
		    new OpenLayers.Layer.Google(
			    "gmap_normal", {
			        type: G_NORMAL_MAP,
			        sphericalMercator: true
			        //  google layer has 20 zoom levels (0 to 19) (from farther to closer)
			    },{
			        buffer: 0,
			        minZoomLevel: 0,
			        maxZoomLevel: 17
			        // we therefore limit the number of zoom levels to 18
			    }
		    )
		];
	},
	
	getIgnLayers: function(config) {
	    var myGeoRM = Geoportal.GeoRMHandler.addKey(this.baseConfig.gpKey,'http://jeton-api.ign.fr/', 100, this.map);

        return [
            new Geoportal.Layer.WMSC(
	            "ign_map",
	            "http://wxs.ign.fr/geoportail/wmsc",
	            {
	                layers:'GEOGRAPHICALGRIDSYSTEMS.MAPS',
	                format:'image/jpeg',
	                exceptions:"text/xml"
	            },
	            {
	                gridOrigin: new OpenLayers.LonLat(0,0),
	                isBaseLayer: true,
                    visibility: false,
                    buffer: 1, 
	                GeoRM: myGeoRM
	            }
	        ),
	        new Geoportal.Layer.WMSC(
	            "ign_orthos",
	            "http://wxs.ign.fr/geoportail/wmsc",
	            {
	                layers:'ORTHOIMAGERY.ORTHOPHOTOS',
	                format:'image/jpeg',
	                exceptions:"text/xml"
	            },
	            {
	                gridOrigin: new OpenLayers.LonLat(0,0),
	                isBaseLayer: true,
                    visibility: false,
                    buffer: 1, 
	                GeoRM: myGeoRM
	            }
	        )	
        ];
    },
    
    getLayerTreeModel: function() {
	    var providerTreeModel;
	    switch (this.provider) {
		    case 'ign':
		        providerTreeModel = this.getIgnLayerTreeModel();
		        break;
		    default:
		        providerTreeModel = this.getGmapLayerTreeModel();
	    }
	
	    var tm = [{
	        text: OpenLayers.i18n('Camptocamp.org'),
	        expanded: true,
	        children: [{
		        text: OpenLayers.i18n('Sommets'),
		        checked: true,
		        layerName: 'c2corg:summits',
		        icon: mapfish.Util.getIconUrl(this.baseConfig.wmsUrl, {layer: 'summits'})
		    },{
			    text: OpenLayers.i18n('Accès'),
		        checked: false,
		        layerName: 'c2corg:parkings',
		        icon: mapfish.Util.getIconUrl(this.baseConfig.wmsUrl, {layer: 'parkings'})
	        }]
	    },{
	        text: OpenLayers.i18n('Arrière-plans'),
	        expanded: true,
		    children: providerTreeModel
	    }];
	
	    return tm;
	},
	
	getGmapLayerTreeModel: function() {
	    return [{
		    text: OpenLayers.i18n('Relief'),
		    checked: true,
		    layerName: 'gmap_physical',
		    id: 'gmap_physical'
		},{
		    text: OpenLayers.i18n('Mixte'),
		    checked: false,
		    layerName: 'gmap_hybrid',
		    id: 'gmap_hybrid'	
		},{
		    text: OpenLayers.i18n('Satellite'),
		    checked: false,
		    layerName: 'gmap_satellite',
		    id: 'gmap_satellite'
		},{
		    text: OpenLayers.i18n('Normal'),
		    checked: false,
		    layerName: 'gmap_normal',
		    id: 'gmap_normal'	
	    }];
	},
	
	getIgnLayerTreeModel: function() {
        return [{
            text: OpenLayers.i18n('Cartes IGN'),
            checked: true,
            layerName: 'ign_map',
            id: 'ign_map',
            minResolution: 2 
        },{
            text: OpenLayers.i18n('Orthophotos IGN'),
            checked: false,
            id: 'ign_orthos',
            layerName: 'ign_orthos'
        }];
    },

    initLinkPanel: function() {
        this.linkPanel = new Ext.FormPanel({
            renderTo: 'linkContainer',
            width: 450,
            title: OpenLayers.i18n('Map URL'),
            border: false,
            labelAlign: 'top',
            items: [
                {
                    xtype: 'textfield',
                    hideLabel: true,
                    width: 440,
                    id: 'permalink',
                    listeners: {
                        'focus': function() {
                            this.selectText();
                        }
                    }
                }
            ]
        });
    },

    switchProvider: function() {
	    this.map.destroy();
	    this.includeProviderLib(this.provider);
	    this.createMap();
	
	    // TODO: update only layertree panel
	    var sidepanel = Ext.getCmp('sidepanel');
	    sidepanel.items = this.createLayerTree();
	    sidepanel.doLayout();
    }

/*
    includeProviderLib: function(providerId) {
	    return; // neutralized because does not seem to work // FIXME
	
	    if (!this.includedProviderLibs) this.includedProviderLibs = [];
	    if (this.includedProviderLibs.indexOf(providerId) != -1) return; // already included
	    
	    switch (providerId) {
		    case 'ign':
		        var gpLib = 'http://api.ign.fr/api?v=1.0beta4&key=' + this.baseConfig.gpKey + '&includeEngine=false&instance=pipo';
		        this.includeScriptFile(gpLib);
		        this.includeScriptFile(this.baseUrl + 'static/js/mapfish/geoportal/GeoportalMin_v2.js');
		        break;
		    case 'gmap':
		        var gmLib = 'http://maps.google.com/maps?file=api&v=2&key=' + this.baseConfig.gmKey;
		        this.includeScriptFile(gmLib);
		        break;
	    }
	    this.includedProviderLibs.push(providerId);
    },

    includeScriptFile: function(file) {
	    if (/MSIE/.test(navigator.userAgent) || /Safari/.test(navigator.userAgent)) {
            document.write('<script src="' + file + '"></script>');
        } else {
            var s = document.createElement("script");
            s.src = file;
            var h = document.getElementsByTagName("head").length ? 
                       document.getElementsByTagName("head")[0] : 
                       document.body;
            h.appendChild(s);
        }
    }
*/
});
