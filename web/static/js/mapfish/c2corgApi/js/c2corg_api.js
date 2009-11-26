Ext.namespace("c2corg");

// TODO: call lib only when needed
document.write('<script type="text/javascript" src="http://api.ign.fr/api?v=1.0beta4-m&key=');
document.write(c2corg.config.gpKey + '&includeEngine=false"></script>');
document.write('<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=');
document.write(c2corg.config.gmKey + '"></script>');

c2corg.API = OpenLayers.Class(MapFish.API, {

    lang: 'fr',
    ignGRM: null,

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

        this.ignGRM = gGEOPORTALRIGHTSMANAGEMENT;

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

        return this.map;
    },

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
        var controls = [
            new OpenLayers.Control.PanZoomBar(),
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.MousePosition(/*{
                div: $('mousepos'),
                numDigits: 6,
                prefix: OpenLayers.i18n('longitude / latitude: '),
                displayProjection: this.provider == 'gmap' ? this.epsg900913 : this.epsg4326
            }*/)
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
/*
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
            */
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
                    projection: this.epsg4326,
                    units: 'degrees',
                    visibility: false,
                    isBaseLayer: false
                }
            )
	    ];

        layers = layers.concat(this.getGmapLayers());
        layers = layers.concat(this.getIgnLayers());
        return layers;
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
            new OpenLayers.Layer.OSM()
		];
	},
	
	getIgnLayers: function(config) {
        var apiKey = this.ignGRM.apiKey;
        var myGeoRM = Geoportal.GeoRMHandler.addKey(apiKey,
            this.ignGRM[apiKey].tokenServer.url,
            this.ignGRM[apiKey].tokenServer.ttl,
            this.map);

        return [
            new Geoportal.Layer.WMSC(
	            "ign_map",
	            this.ignGRM[apiKey].resources['GEOGRAPHICALGRIDSYSTEMS.MAPS:WMSC'].url,
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
	            this.ignGRM[apiKey].resources['ORTHOIMAGERY.ORTHOPHOTOS:WMSC'].url,
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
	        text: OpenLayers.i18n('Backgrounds'),
	        expanded: true,
		    children: [{
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
    		    text: OpenLayers.i18n('Normal'),
    		    checked: false,
    		    layerName: 'gmap_normal',
    		    id: 'gmap_normal'
            },{
                text: OpenLayers.i18n('OpenStreetMap'),
                checked: false,
                layerName: 'OpenStreetMap',
                id: 'osm'
            },{
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
	        }]
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
                        if (geom instanceof OpenLayers.Geometry.Point) {
                            geom.transform(lp, bl.projection);
                        } else if (geom instanceof OpenLayers.Geometry.Collection) {
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
