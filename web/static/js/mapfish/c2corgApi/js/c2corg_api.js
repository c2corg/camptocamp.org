Ext.namespace("c2corg");

var pipo = ''; // FIXME: really needed?
document.write('<script type="text/javascript" src="http://api.ign.fr/api?v=1.0beta4&key=');
document.write(c2corg.config.gpKey + '&includeEngine=false&instance=pipo"></script>');

c2corg.API = OpenLayers.Class(MapFish.API, {

    lang: 'fr',
    epsg4326: new OpenLayers.Projection("EPSG:4326"),
    miller: new OpenLayers.Projection("IGNF:MILLER"),
    fxx: new OpenLayers.Projection("IGNF:GEOPORTALFXX"),

    initialize: function(config) {
        MapFish.API.prototype.initialize.apply(this, arguments);

        this.baseConfig = c2corg.config;
        if (config) {
            Ext.apply(this.baseConfig, config);
        }

        Ext.BLANK_IMAGE_URL = this.baseConfig.baseUrl + '/static/js/mapfish/mfbase/ext/resources/images/default/s.gif';
		OpenLayers.ImgPath = this.baseConfig.baseUrl + '/static/images/openlayers/';
    },

    /* public methods */

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
                displayProjection: this.epsg4326
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
	    var myGeoRM = Geoportal.GeoRMHandler.addKey(this.baseConfig.gpKey,'http://jeton-api.ign.fr/', 100, this.map);

        return [
            new OpenLayers.Layer("void-layer", {
                isBaseLayer: true,
                visibility: false
            }),
            new Geoportal.Layer.WMSC(
	            "ign",
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
	            "orthophotos",
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
	        ),
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
            text: OpenLayers.i18n('Arrière-plans'),
            expanded: true,
            children: [{
                text: OpenLayers.i18n('Cartes IGN'),
                checked: true,
                layerName: 'ign',
                id: 'scans',
                minResolution: 2 
            },{
                text: OpenLayers.i18n('Orthophotos IGN'),
                checked: false,
                id: 'orthos',
                layerName: 'orthophotos'
            },{
                text: OpenLayers.i18n('None'),
                checked: false,
                layerName: 'void-layer'
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
