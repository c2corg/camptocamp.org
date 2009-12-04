Ext.namespace("c2corg");

// TODO: call lib only when needed
document.write('<script type="text/javascript" src="http://api.ign.fr/api?v=1.0beta4-m&key=');
document.write(c2corg.config.gpKey + '&includeEngine=false"></script>');
document.write('<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=');
document.write(c2corg.config.gmKey + '"></script>');

c2corg.API = OpenLayers.Class(MapFish.API, {

    lang: 'fr',

    epsg4326: new OpenLayers.Projection("EPSG:4326"),
    miller: new OpenLayers.Projection("IGNF:MILLER"),
    fxx: new OpenLayers.Projection("IGNF:GEOPORTALFXX"),
    epsg900913: new OpenLayers.Projection("EPSG:900913"),

    query: null,
    overview: null,

    initialize: function(config) {
        config = config || {};
        MapFish.API.prototype.initialize.apply(this, arguments);

        this.baseConfig = c2corg.config;
        if (config) {
            Ext.apply(this.baseConfig, config);
        }

        Ext.BLANK_IMAGE_URL = this.baseConfig.baseUrl + '/static/js/mapfish/mfbase/ext/resources/images/default/s.gif';
		OpenLayers.ImgPath = this.baseConfig.baseUrl + '/static/images/openlayers/';
    },

    /* public methods */
    
    createMap: function(config) {
        config = config || {};

        var options = this.getMapOptions();
        if (config.div) {
            options.div = config.div;
        }

        var controls = this.getControls(config);
        if (controls) {
            options.controls = controls;
        }

        //this.map = new OpenLayers.Map(options);
        this.map = new c2corg.Map(options);

        var layers = this.getLayers(config);
     
        // Create always a draw layer on top
        this.drawLayer = this.getDrawingLayer();
        layers.push(this.drawLayer);

        this.map.addLayers(layers);

        this.drawLayer.setZIndex(this.map.Z_INDEX_BASE['Feature']);

        // Put Drawing Layer always on top (Map.setLayerIndex reorder always ALL layers)
        this.map.events.on({
            scope: this.drawLayer,
            changelayer: function(evt) {
                if (evt.property == "order") {
                    this.setZIndex(this.map.Z_INDEX_BASE['Feature']);
                }    
            }    
        });

        // add test vector features
        var p = new OpenLayers.Geometry.Point(6.780357, 46.262455).transform(this.epsg4326, this.epsg900913);
        var pf = new OpenLayers.Feature.Vector(p);
        this.drawLayer.addFeatures([pf]);

        if (!this.map.getCenter()) {
            if (config.easting && config.northing) {
                this.map.setCenter(
                    new OpenLayers.LonLat(config.easting, config.northing).
                                   transform(this.epsg4326, this.epsg900913),
                    config.zoom);
            } else if (config.bbox) {
                this.map.zoomToExtent(
                    new OpenLayers.Bounds.fromArray(config.bbox).
                                          transform(this.epsg4326, this.epsg900913, true));
            } else if (this.baseConfig.initialExtent) {
                this.map.zoomToExtent(
                    new OpenLayers.Bounds.fromArray(this.baseConfig.initialExtent).
                                          transform(this.epsg4326, this.epsg900913, true));
            } else {
                this.map.zoomToMaxExtent();
            }
        }

        new c2corg.API.Tooltip({api: this});

        if (this.isMainApp) {
            this.overview.maximizeControl();
        }

        return this.map;
    },

    createToolbar: function(config) {
	
        if (!config) {
            config = {
                items: ['ZoomToMaxExtent', 'Navigation', 'ZoomBox', 'NavigationHistory', 'Separator', 'LengthMeasure']
            }
        }
        var action, items = MapFish.API.prototype.createToolbar.apply(this, [config]);

        if (this.isMainApp) {
            // query tool
            items.push(new GeoExt.Action({
                control: this.getQuery().control,
                toggleGroup: 'navigation',
                allowDepress: false,
                iconCls: 'info'
            }));

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

        return items;
    },

    /* private methods */

    getMapOptions: function() {
	    return {
	        projection: this.epsg900913,
            displayProjection: this.epsg4326,
	        units: "m",
	        maxResolution: 156543.0339,
	        maxExtent: new OpenLayers.Bounds(-20037508, -136554022,
	                                         20037508, 136554022)
	    };	
	},
	
    getControls: function(config) {
        var options = this.getMapOptions();

        var osmLayer = new OpenLayers.Layer.OSM();
        osmLayer.buffer = 0;

        this.overview = new OpenLayers.Control.OverviewMap({
            layers: [osmLayer],
            size: new OpenLayers.Size(180, 120),
            minRectSize: 8,
            minRatio: 16, 
            maxRatio: 64, 
            mapOptions: options
        });

        var controls = [
            new OpenLayers.Control.PanZoomBar(),
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.MousePosition({
                div: $('mousepos'),
                numDigits: 6,
                prefix: OpenLayers.i18n('longitude / latitude: '),
                displayProjection: this.epsg4326
            }),
            this.overview,
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
                    layers: ['summits', 'parkings', 'huts', 'sites', 'users', 'images', 'routes',
                             'outings', 'ranges', 'countries', 'departements', 'maps'], 
                    format: 'image/png', 
                    transparent: true
                }, {
                    maxResolution: 2048,
                    numZoomLevels: 13,
                    singleTile: true,
                    projection: this.epsg4326,
                    units: 'degrees',
                    visibility: false,
                    isBaseLayer: false
                }
            )
	    ];

        layers = layers.concat(this.getBgLayers());
        layers = layers.concat(this.getIgnLayers());
        return layers;
	},
	
	getBgLayers: function(config) {
        var osmLayer = new OpenLayers.Layer.OSM();
        osmLayer.buffer = 0;

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
		    ),
            osmLayer
		];
	},
	
	getIgnLayers: function(config) {
        if (!gGEOPORTALRIGHTSMANAGEMENT) return [];

        var apiKey = gGEOPORTALRIGHTSMANAGEMENT.apiKey;
        var myGeoRM = Geoportal.GeoRMHandler.addKey(apiKey,
            gGEOPORTALRIGHTSMANAGEMENT[apiKey].tokenServer.url,
            gGEOPORTALRIGHTSMANAGEMENT[apiKey].tokenServer.ttl,
            this.map);

        return [
            new Geoportal.Layer.WMSC(
	            "ign_map",
	            gGEOPORTALRIGHTSMANAGEMENT[apiKey].resources['GEOGRAPHICALGRIDSYSTEMS.MAPS:WMSC'].url,
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
                    resolutions: Geoportal.Catalogue.RESOLUTIONS.slice(5,18),
                    alwaysInRange: true,
	                projection: this.fxx,
                    units: this.fxx.getUnits(),
                    GeoRM: myGeoRM
	            }
	        ),
	        new Geoportal.Layer.WMSC(
	            "ign_orthos",
	            gGEOPORTALRIGHTSMANAGEMENT[apiKey].resources['ORTHOIMAGERY.ORTHOPHOTOS:WMSC'].url,
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
                    resolutions: Geoportal.Catalogue.RESOLUTIONS.slice(5,18),
                    alwaysInRange: true,
                    projection: this.fxx,
                    units: this.fxx.getUnits(),
	                GeoRM: myGeoRM
	            }
            )
        ];
    },
    
    getLayerTreeModel: function() {
	    return [{
	        text: OpenLayers.i18n('Camptocamp.org'),
	        expanded: true,
	        children: [{
                text: OpenLayers.i18n('Summits'),
                checked: true,
                layerName: 'c2corg:summits',
                icon: this.getPictoUrl('summits')
            },{
			    text: OpenLayers.i18n('Parkings'),
		        checked: false,
		        layerName: 'c2corg:parkings',
		        icon: this.getPictoUrl('parkings')
            },{
                text: OpenLayers.i18n('Huts'),
                checked: false,
                layerName: 'c2corg:huts',
                icon: this.getPictoUrl('huts')
            },{
                text: OpenLayers.i18n('Sites'),
                checked: false,
                layerName: 'c2corg:sites',
                icon: this.getPictoUrl('sites')
            },{
                text: OpenLayers.i18n('Users'),
                checked: false,
                layerName: 'c2corg:users',
                icon: this.getPictoUrl('users')
            },{
                text: OpenLayers.i18n('Images'),
                checked: false,
                layerName: 'c2corg:images',
                icon: this.getPictoUrl('images')
            },{
                text: OpenLayers.i18n('Routes'),
                checked: false,
                layerName: 'c2corg:routes',
                icon: this.getPictoUrl('routes')
            },{
                text: OpenLayers.i18n('Outings'),
                checked: false,
                layerName: 'c2corg:outings',
                icon: this.getPictoUrl('outings')
            },{
                text: OpenLayers.i18n('Ranges'),
                checked: false,
                layerName: 'c2corg:ranges',
                icon: this.getPictoUrl('areas')
            },{
                text: OpenLayers.i18n('Maps'),
                checked: false,
                layerName: 'c2corg:maps',
                icon: this.getPictoUrl('maps')
            },{
                text: OpenLayers.i18n('Countries'),
                checked: false,
                layerName: 'c2corg:countries',
                icon: this.getPictoUrl('areas')
            },{
                text: OpenLayers.i18n('Admin boundaries'),
                checked: false,
                layerName: 'c2corg:departements',
                icon: this.getPictoUrl('areas')
	        }]
	    },{
	        text: OpenLayers.i18n('Backgrounds'),
	        expanded: true,
		    children: [{
		        text: OpenLayers.i18n('Relief'),
		        checked: true,
    		    layerName: 'gmap_physical',
                iconCls: 'bglayerIcon',
    		    id: 'gmap_physical'
    		},{
    		    text: OpenLayers.i18n('Mixte'),
    		    checked: false,
    		    layerName: 'gmap_hybrid',
                iconCls: 'bglayerIcon',
    		    id: 'gmap_hybrid'	
    		},{
    		    text: OpenLayers.i18n('Normal'),
    		    checked: false,
    		    layerName: 'gmap_normal',
                iconCls: 'bglayerIcon',
    		    id: 'gmap_normal'
            },{
                text: OpenLayers.i18n('OpenStreetMap'),
                checked: false,
                layerName: 'OpenStreetMap',
                iconCls: 'bglayerIcon',
                id: 'osm'
            },{
                text: OpenLayers.i18n('Cartes IGN'),
                checked: true,
                layerName: 'ign_map',
                iconCls: 'bglayerIcon',
                id: 'ign_map',
                minResolution: 2 
            },{
                text: OpenLayers.i18n('Orthophotos IGN'),
                checked: false,
                iconCls: 'bglayerIcon',
                id: 'ign_orthos',
                layerName: 'ign_orthos'
	        }]
	    }];
	},

    getPictoUrl: function(name) {
        return this.baseConfig.baseUrl + '/static/images/modules/' + name + '_mini.png';
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

    getQuery: function() {
        if (!this.query) {
            this.query = new c2corg.Query({'api': this});
        }
        return this.query;
    },

    getDrawingLayer: function() {
        if (!this.drawLayer) {
           var context = { 
                getIcon: function(feature) {
                    if (feature.geometry instanceof OpenLayers.Geometry.Point) {
                        //return this.baseConfig.baseUrl + "static/images/mapmarker.png"; // FIXME: this is not defined
                        return "/static/images/mapmarker.png";
                    }
                    return null;
                }
            };
            var myStyles = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
                    pointRadius: "10",
                    fillColor: "#FFFF00",
                    fillOpacity: 0.8, 
                    strokeColor: "#FF8000",
                    strokeOpacity: 0.8, 
                    strokeWidth: 2,

                    externalGraphic: "${getIcon}",
                    graphicWidth: 32, 
                    graphicHeight: 32, 
                    graphicYOffset: -30
                }, {context: context})
            });
            this.drawLayer = new OpenLayers.Layer.Vector("Drawings layer",
            {
                displayInLayerSwitcher: false,
                styleMap: myStyles
            });
            if (!this.selectCtrl) {
                this.selectCtrl = new OpenLayers.Control.SelectFeature(this.drawLayer);
                this.map.addControl(this.selectCtrl);
                this.selectCtrl.activate();
                this.drawLayer.events.on({
                    featureselected: function(e) {
                        if (this.activatePopup) {
                            this.showPopup({
                                feature: e.feature
                            });
                        };
                        document.body.style.cursor = 'default';
                    },
                    scope: this 
                });
            }
        }
        return this.drawLayer;
    },

    /** 
     * get list of activated layers that may be queried
     */
    getEnabledQueryableLayers: function() {
        var layers = []; 
        var olLayers = this.map.getLayersByClass('OpenLayers.Layer.WMS');
        for (var i = 0; i < olLayers.length; ++i) {
            var cur = olLayers[i];
    
            if (cur.getVisibility()) {
                for (var j = 0; j < cur.params.LAYERS.length; ++j) {
                    layers.push(cur.params.LAYERS[j]);
                }
            }    
        }

        return layers;
    }
});

/**
 * Extension of class OpenLayers.Map to handle base layers with different projections
 * Based on Shama's code at http://shamavideals.l-wa.org/oltest/testMultiProviderMap.html
 */
c2corg.Map = OpenLayers.Class(OpenLayers.Map, {

    /**
     * Overrides OpenLayers.Map.setBaseLayer()
     */
    setBaseLayer: function(newBaseLayer) {

        if (newBaseLayer == this.baseLayer) return;

        var oldBaseLayer = null, oldProjection = null, oldExtent = null;
        if (this.baseLayer) {
            oldBaseLayer = this.baseLayer;
            oldProjection = this.getProjection();
            oldExtent = this.baseLayer.getExtent();
        }

        // is newBaseLayer an already loaded layer?
        if (OpenLayers.Util.indexOf(this.layers, newBaseLayer) != -1) {
            
            // make the old base layer invisible
            if (this.baseLayer != null) { 
                this.baseLayer.setVisibility(false);
            }

            // set new baselayer
            this.baseLayer = newBaseLayer;

            // Increment viewRequestID since the baseLayer is
            // changing. This is used by tiles to check if they should
            // draw themselves.
            this.viewRequestID++;
            this.baseLayer.visibility = true;

            //redraw all layers
            var center = this.getCenter();
            if (center != null) {
                
                //either get the center from the old Extent or just from
                // the current center of the map.
                var newCenter = (oldExtent)
                    ? oldExtent.getCenterLonLat()
                    : center;

                // reproject new center of the map
                newCenter.transform(oldProjection, this.getProjection());
                
                //the new zoom will either come from the old Extent or
                // from the current resolution of the map
                var newZoom = (oldExtent)
                        ? this.getZoomForExtent(oldExtent, true)
                        : this.getZoomForResolution(this.resolution, true);
                
                // zoom and force zoom change
                this.setCenter(newCenter, newZoom, false, true);
            }
        }

        // reproject vector layers
        this.updateVectorLayers();

        this.events.triggerEvent("changebaselayer", {
            layer: newBaseLayer,
            baseLayer: oldBaseLayer
        });
    },
    
    /**
     * Overrides OpenLayers.Map.addLayer()
     */
    addLayer: function(layer) {
        this.updateVectorLayers(layer);
        OpenLayers.Map.prototype.addLayer.apply(this, arguments);
    },    
 
    updateVectorLayers: function(layers) {
        if (!this.baseLayer || !this.baseLayer.projection) return;

        layers = layers ? [layers] : this.layers;
        var bl = this.baseLayer;

        for (var i = 0, len = layers.length; i < len; i++) {
            var layer = layers[i];
            // for every vector layer...
            if (layer && layer instanceof OpenLayers.Layer.Vector) {
                var lp = layer.projection;
                // if its projection is different from the one of the current base layer...
                if (!bl.projection.equals(lp)) {
                    // reproject all its features
                    for (var j = 0, flen = layer.features.length; j < flen; j++) {
                        var geom = layer.features[j].geometry;
                        if (!geom) continue;
                        if (geom instanceof OpenLayers.Geometry.Point ||
                            geom instanceof OpenLayers.Geometry.Collection) {
                            geom.transform(lp, bl.projection);
                        }
                        // other geometry types are not yet supported
                    }
                    // then update layer projection
                    layer.projection = bl.projection;
                    layer.redraw();
                }
            }
        }
    }
});
