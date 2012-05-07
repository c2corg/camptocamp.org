/**
 * @include c2corgApi/js/ArgParser.js
 * @include c2corgApi/js/tooltip.js
 * @include c2corgApi/js/somerc.js
 * @include c2corgApi/js/C2CGeonamesSearchCombo.js
 * @requires c2corgApi/js/Permalink.js
 * @requires MapFishApi/js/mapfish_api.js
 * @requires MapFishApi/js/Measure.js
 * @requires MapFishApi/js/ZoomToExtent.js
 * @requires OpenLayers/BaseTypes.js
 * @requires OpenLayers/Control/Attribution.js
 * @requires OpenLayers/Control/MousePosition.js
 * @requires OpenLayers/Control/Navigation.js
 * @requires OpenLayers/Control/OverviewMap.js
 * @requires OpenLayers/Control/PanZoomBar.js
 * @requires OpenLayers/Control/Scale.js
 * @requires OpenLayers/Control/ScaleLine.js
 * @requires OpenLayers/Control/SelectFeature.js
 * @requires OpenLayers/Geometry/Collection.js
 * @requires OpenLayers/Geometry/Point.js
 * @requires OpenLayers/Lang.js
 * @requires OpenLayers/Layer/GML.js
 * @requires OpenLayers/Layer/Google.js
 * @requires OpenLayers/Layer/Vector.js
 * @requires OpenLayers/Layer/WMS.js
 * @requires OpenLayers/Layer/WMTS.js
 * @requires OpenLayers/Layer/XYZ.js
 * @requires OpenLayers/Layer/Markers.js
 * @requires OpenLayers/Format/WMTSCapabilities.js
 * @requires OpenLayers/Format/WMTSCapabilities/v1_0_0.js
 * @requires OpenLayers/Map.js
 * @requires OpenLayers/Marker.js
 * @requires OpenLayers/Projection.js
 * @requires OpenLayers/Style.js
 * @requires OpenLayers/StyleMap.js
 * @requires OpenLayers/Util.js
 * @requires geoportal/GeoportalMin.js
 * @requires geoportal/Logo.js
 */

Ext.namespace("c2corg");

// due to some "bug" in GeoportalMin.js we make sure that the ca and eu languages are defined
Geoportal.Lang.ca={};
Geoportal.Lang.eu={};

Proj4js.defs["EPSG:21781"] = "+title=CH1903 / LV03 +proj=somerc +lat_0=46.95240555555556 +lon_0=7.439583333333333 +x_0=600000 +y_0=200000 +ellps=bessel +towgs84=674.374,15.056,405.346,0,0,0,0 +units=m +no_defs";

c2corg.API = OpenLayers.Class(MapFish.API, {

    lang: 'fr',

    epsg4326: new OpenLayers.Projection("EPSG:4326"),
    miller: new OpenLayers.Projection("IGNF:MILLER"),
    fxx: new OpenLayers.Projection("IGNF:GEOPORTALFXX"),
    epsg900913: new OpenLayers.Projection("EPSG:900913"),
    epsg21781: new OpenLayers.Projection("EPSG:21781"),

    overview: null,

    argParserCenter: null,

    tooltip: null,
    tooltipTest: null,
    
    initialBgLayer: null,
    
    ignLoaded: false,
    
    swisstopoLayer: null,

    initialize: function(config) {
        config = config || {};
        MapFish.API.prototype.initialize.apply(this, arguments);

        this.baseConfig = c2corg.config;
        if (config) {
            Ext.apply(this.baseConfig, config);
        }

        Ext.BLANK_IMAGE_URL = this.baseConfig.staticBaseUrl + '/static/js/mapfish/mfbase/ext/resources/images/default/s.gif';
        OpenLayers.ImgPath = this.baseConfig.staticBaseUrl + '/static/js/mapfish/mfbase/openlayers/img/';
        this.updateOpenLayersImgPath(false);
        
        // addition of the app translations
        if (typeof(c2corg_map_translations) != 'undefined') {
            if (this.lang == 'eu') {
                // OpenLayers i18n for euskara is not available yet
                OpenLayers.Lang.eu = c2corg_map_translations; 
            } else {
                OpenLayers.Util.extend(OpenLayers.Lang[this.lang], c2corg_map_translations);
            }
        }
        
        this.initialBgLayer = 'gmap_physical';
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
        if (this.initialBgLayer == 'swisstopo_map') {
            this.setSwisstopoLayer(true);
        } else if (['ign_map', 'ign_orthos'].indexOf(this.initialBgLayer) != -1) {
            // IGN layers are loaded only when asked by user, in order to retrieve a token only when necessary
            this.setIgnLayers(this.initialBgLayer, true);
        } else {
            this.setBaseLayerByName(this.initialBgLayer);
        }

        this.drawLayer.setZIndex(this.map.Z_INDEX_BASE['Feature']);
        
        this.map.events.on({
            scope: this,
            changelayer: function(evt) {
                // Put Drawing Layer always on top (Map.setLayerIndex reorder always ALL layers)
                if (evt.property == "order") {
                    this.drawLayer.setZIndex(this.map.Z_INDEX_BASE['Feature']);
                }    
            },
            move: function(evt) {
                if (this.map.baseLayer.name == 'ign_map' && this.map.getResolution() < 2) {
                    this.initialBgLayer = 'ign_orthos';
                    this.setBaseLayerByName(this.initialBgLayer);
                }
            }
        });

        if (this.argParserCenter) {
            this.centerOnArgParserCenter();
        }

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

        this.tooltip = new c2corg.API.Tooltip({
            api: this,
            map: this.map
        });
        this.tooltip.activate();
        this.map.addControl(this.tooltip);

        this.tooltipTest = new c2corg.API.TooltipTest({
            api: this,
            map: this.map
        });
        this.tooltipTest.activate();
        this.map.addControl(this.tooltipTest);

        if (this.isMainApp) {
            this.overview.maximizeControl();
        }

        return this.map;
    },

    createLayerTree: function(config) {
        MapFish.API.prototype.createLayerTree.apply(this, arguments);
        if (this.isMainApp) {
            this.updateLayerTreeFromPermalink();
            // neutralized because done in layout.js
            /*
            if (this.argParserCenter && this.layerTreeNodes.length > 0 && (
                    this.layerTreeNodes.indexOf('ign_map') != -1 ||
                    this.layerTreeNodes.indexOf('ign_orthos') != -1
            )) {
                // update center when base layer is an IGN one (different projection)
                // FIXME: do it automatically somewhere?
                this.centerOnArgParserCenter();
            }
            */
        }
        return this.tree;
    },

    createToolbar: function(config) {
        if (!config) {
            config = {
                items: ['ZoomToMaxExtent', 'Navigation', 'ZoomBox', 'NavigationHistory', 'Separator', 'LengthMeasure']
            }
        }
        return MapFish.API.prototype.createToolbar.apply(this, [config]);
    },

    initZoomBox: function (config) {
        var control = new OpenLayers.Control.ZoomBox(config.controls);
        control.events.on({
            'activate': this.deactivateTooltip,
            'deactivate': this.activateTooltip,
            scope: this
        });
        // FIXME: in some cases tooltip is reactivated whereas zoombox is still active
        
        var action = new GeoExt.Action(Ext.apply({
            map: this.map,
            control: control,
            toggleGroup: config.toggleGroup || 'navigation',
            allowDepress: false,
            tooltip: OpenLayers.i18n('zoom box'),
            iconCls: 'zoomin'
        }, config.actions));
        this.tools.push(action);
    },

    initLengthMeasure: function (config) {
        var measure = new MapFish.API.Measure(config.controls);
        var control = measure.createLengthMeasureControl();
        control.events.on({
            'activate': this.deactivateTooltip,
            'deactivate': this.activateTooltip,
            scope: this
        });
        
        var action = new GeoExt.Action(Ext.apply({
            map: this.map,
            control: control,
            toggleGroup: config.toggleGroup || 'navigation',
            allowDepress: false,
            tooltip: OpenLayers.i18n('length measure'),
            iconCls: 'measureLength'
        }, config.actions));
        this.tools.push(action);
    },
    
    createBbar: function(config) {
        config = config || {};

        var mousePosition = new OpenLayers.Control.MousePosition({
            emptyString: '',
            numDigits: 6,
            prefix: OpenLayers.i18n('longitude / latitude: '),
            displayProjection: this.epsg4326
        });
        this.map.addControl(mousePosition);
        
        // TODO: add status bar? See GeoExt.ux.LoadingStatusBar
        
        // FIXME: generate dynamically a scale container instead of hardcoding it in HTML
        var scale = new OpenLayers.Control.Scale($('scale'), {
            updateScale: function() {
                var scale = this.map.getScale();
                if (!scale) {
                    return;
                }
                this.element.innerHTML = OpenLayers.i18n("Scale = 1 : ${scaleDenom}", {
                    scaleDenom: OpenLayers.Number.format(scale, 0, "'")
                });
            }
        });
        this.map.addControl(scale);
        
        config = Ext.apply({
            height: '20px',
            items: [
                ' ', OpenLayers.i18n('Backgrounds'), this.createBgLayersCombo(), 
                ' ', mousePosition.draw(), '->', $('scale'), ' '
            ]
        }, config);
        
        return new Ext.Toolbar(config);
    },
    
    createBgLayersCombo: function(config) {
        config = config || {};
        
        var bgLayers = [
            ['gmap_physical', OpenLayers.i18n('Physical')],
            ['gmap_hybrid', OpenLayers.i18n('Hybrid')],
            ['gmap_normal', OpenLayers.i18n('Normal')],
            ['OpenStreetMap', OpenLayers.i18n('OpenStreetMap')],
            ['ign_map', OpenLayers.i18n('IGN maps')],
            ['ign_orthos', OpenLayers.i18n('IGN orthos')],
            ['swisstopo_map', OpenLayers.i18n('Swisstopo maps')]
        ];
        
        var store = new Ext.data.SimpleStore({
            fields: ['id', 'name'],
            data: bgLayers
        });
        
        return new Ext.form.ComboBox({
            id: 'bgLayer',
            editable: false,
            width: 140,
            store: store,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            value: this.initialBgLayer,
            triggerAction: 'all',
            mode: 'local',
            listeners: {
                select: function(combo, record, index) { // called when the user switches layer
                    var layername = record.data.id;

                    this.updateC2corgLayer(layername);
                    
                    if (layername == 'swisstopo_map' && !this.swisstopoMapLayer) {
                        this.setSwisstopoLayer(); // uses async call
                        return;
                    }
                    
                    if (['ign_map', 'ign_orthos'].indexOf(layername) != -1 && !this.ignLoaded) {
                        this.setIgnLayers(layername);
                        return;
                    }

                    this.setBaseLayerByName(layername);
                },
                scope: this
            }
        });
    },

    /* private methods */

    getMapOptions: function() {
        return {
            projection: this.epsg900913,
            displayProjection: this.epsg4326,
            units: "m",
            theme: null,
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
            maxRatio: 32, 
            mapOptions: options
        });

        var controls = [
            this.isMainApp ? new OpenLayers.Control.PanZoomBar() : new OpenLayers.Control.PanZoom(),
            new OpenLayers.Control.Navigation({zoomWheelEnabled: this.isMainApp}),
            new OpenLayers.Control.ScaleLine(),
            this.overview,
            new Geoportal.Control.Logo({logoSize: Geoportal.Control.Logo.WHSizes.mini}),
            new OpenLayers.Control.Attribution(),
            new c2corg.API.GpLogo({api: this})
        ];
        
        // Permalink control is added later because it needs the toolbar to initialize

        if (this.isMainApp) {
            controls.push(new c2corg.API.ArgParser({api: this}));
        }

        return controls;
    },

    updateC2corgLayer: function(baseLayer) {
        var c2corg = this.map.getLayersByName("c2corg")[0];
        
        switch (baseLayer) {
            case "swisstopo_map":
                c2corg.units = "m";
                c2corg.projection = this.epsg21781;
                break;
            case "gmap_physical":
            case "gmap_hybrid":
            case "gmap_normal":
            case "OpenStreetMap":
                c2corg.units = "m";
                c2corg.projection = this.epsg900913;
                break;
            default:
                // TODO use IGN projection
                c2corg.units = "degrees";
                c2corg.projection = this.epsg4326
        }
    },
    
    getLayers: function(config) {
        var provider = config.provider || this.provider;
        
        var c2cLayers = config.layers ||
                        ['summits', 'huts', 'sites', 'users', 'images', 'routes',
                         'outings', 'ranges', 'countries', 'admin_limits', 'maps',
                         'other_access', 'public_transportations', 'products'];
        // WARNING: using config.layers might disable layers listed in the layertree (FIXME?)
    
        var layers = [
            new OpenLayers.Layer.WMS(
                "c2corg",
                this.baseConfig.wmsUrl, {
                    layers: c2cLayers, 
                    format: 'image/png', 
                    transparent: true
                }, {
                    // comment following 2 parameters if we want to see c2c items including at wide zoom levels
                    maxResolution: 2048,
                    numZoomLevels: 13,
                    singleTile: true,
                    transitionEffect: "resize",
                    projection: this.epsg4326,
                    units: 'degrees',
                    visibility: (config.layers && config.layers.length > 0), // true if config.layers are provided, else false
                    isBaseLayer: false
                }
            )
        ];

        layers = layers.concat(this.getBgLayers());
        
        return layers;
    },
    
    getBgLayers: function(config) {
        var osmLayer = new OpenLayers.Layer.OSM();
        osmLayer.buffer = 0;

        return [
            new OpenLayers.Layer.Google(
                "gmap_physical", {
                    //type: google.maps.MapTypeId.TERRAIN,
                    type: G_PHYSICAL_MAP,
                    sphericalMercator: true,
                    buffer: 0,
                    numZoomLevels: 16,
                    animationEnabled: true
                }
            ),
            new OpenLayers.Layer.Google(
                "gmap_hybrid", {
                    //type: google.maps.MapTypeId.HYBRID,
                    type: G_HYBRID_MAP,
                    sphericalMercator: true,
                    buffer: 0,
                    numZoomLevels: 20,
                    animationEnabled: true
                }
            ),
            new OpenLayers.Layer.Google(
                "gmap_normal", {
                    //type: google.maps.MapTypeId.ROADMAP,
                    type: G_NORMAL_MAP,
                    numZoomLevels: 20,
                    buffer: 0,
                    sphericalMercator: true,
                    animationEnabled: true
                }
            ),
            osmLayer
        ];
    },
    
    setIgnLayers: function(layername, fromPermalink) {
        // note: on the geoportal api, they wait for all classes to be loaded using checkApiLoading function
        // this isn't usefull for us since GeoportalMin is included in our js

        // load API keys configuration, then create the layers and trigger the callback
        Geoportal.GeoRMHandler.getConfig([c2corg.config.gpKey], null,null, {
            onContractsComplete: function () {
                var apiKey = gGEOPORTALRIGHTSMANAGEMENT.apiKey;
                var myGeoRM = Geoportal.GeoRMHandler.addKey(apiKey,
                    gGEOPORTALRIGHTSMANAGEMENT[apiKey].tokenServer.url,
                    gGEOPORTALRIGHTSMANAGEMENT[apiKey].tokenServer.ttl,
                    this.map);
                this.map.addLayers([
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
                            buffer: 1,
                            resolutions: Geoportal.Catalogue.RESOLUTIONS.slice(5,18),
                            alwaysInRange: true,
                            projection: this.fxx,
                            units: this.fxx.getUnits(),
                            GeoRM: myGeoRM,
                            originators: [{
                                logo: 'ign',
                                url: 'http://www.ign.fr/'
                            }]
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
                            buffer: 1,
                            resolutions: Geoportal.Catalogue.RESOLUTIONS.slice(5,18),
                            alwaysInRange: true,
                            projection: this.fxx,
                            units: this.fxx.getUnits(),
                            GeoRM: myGeoRM,
                            originators: [{
                                logo: 'spotimage',
                                url: 'http://www.spotimage.fr/'
                            },{
                                logo: 'cnes',
                                url: 'http://www.cnes.fr/'
                            }]
                        }
                    )]
                );
                this.setBaseLayerByName(layername);
                this.ignLoaded = true;
                if (fromPermalink) {
                    this.centerOnArgParserCenter();
                }
            }.bind(this)
        });
    },
    
    setSwisstopoLayer: function(fromPermalink) {
        fromPermalink = fromPermalink || false;

        // TODO: use JSONP to get capabilities directly from the provider
        OpenLayers.Request.GET({
            url: "/static/js/mapfish/swisstopo/wmts-getcapabilities.xml",
            params: {
                SERVICE: "WMTS",
                VERSION: "1.0.0",
                REQUEST: "GetCapabilities"
            },
            success: function(request) {
                var doc = request.responseXML;
                if (!doc || !doc.documentElement) {
                    doc = request.responseText;
                }

                var format = new OpenLayers.Format.WMTSCapabilities();
                var capabilities = format.read(doc);

                // TODO: create this layer by hand to avoid parsing the huge capabilities file?
                this.swisstopoMapLayer = format.createLayer(capabilities, {
                    layer: "ch.swisstopo.pixelkarte-farbe",
                    matrixSet: "21781", // Only this one
                    format: "image/jpeg",
                    name: "swisstopo_map",
                    opacity: 1.0,
                    isBaseLayer: true,
                    requestEncoding: "REST",
                    style: "default",
                    dimensions: ['TIME'],
                    params: {'time': '20110401'},
                    formatSuffix: 'jpeg',
                    resolutions: [650,500,250,100,50,20,10,5,2.5],
                    projection: this.epsg21781
                });
                
                this.map.addLayer(this.swisstopoMapLayer);
                this.setBaseLayerByName('swisstopo_map');

                if (fromPermalink) {
                    this.centerOnArgParserCenter();
                }
            },
            failure: function() {
                alert("Trouble getting capabilities doc");
                OpenLayers.Console.error.apply(OpenLayers.Console, arguments);
            },
            scope: this
        });
    },
    
    getLayerTreeModel: function() {
        return [{
            text: OpenLayers.i18n('c2c data'),
            expanded: true,
            children: [{
                text: OpenLayers.i18n('summits'),
                checked: false,
                layerName: 'c2corg:summits',
                id: 'summits',
                icon: this.getPictoUrl('summits'),
                children: [{
                    text: OpenLayers.i18n('pass'),
                    icon: this.getPictoUrl('pass', true)
                },{
                    text: OpenLayers.i18n('lake'),
                    icon: this.getPictoUrl('lake', true)
                },{
                    text: OpenLayers.i18n('valley'),
                    icon: this.getPictoUrl('crag', true)
                }]
            },{
                text: OpenLayers.i18n('parkings'),
                checked: false,
                id: 'parkings',
                icon: this.getPictoUrl('parkings'),
                expanded: true,
                children: [{
                    text: OpenLayers.i18n('public_transportations'),
                    icon: this.getPictoUrl('parking_green', true),
                    checked: false,
                    layerName: 'c2corg:public_transportations',
                    id: 'public_transportations'
                },{
                    text: OpenLayers.i18n('other access'),
                    icon: this.getPictoUrl('parkings'),
                    checked: false,
                    layerName: 'c2corg:other_access',
                    id: 'other_access'
                }]
            },{
                text: OpenLayers.i18n('huts'),
                checked: false,
                layerName: 'c2corg:huts',
                id: 'huts',
                icon: this.getPictoUrl('huts'),
                children: [{
                    text: OpenLayers.i18n('camping area'),
                    icon: this.getPictoUrl('camp', true)
                },{
                    text: OpenLayers.i18n('gite'),
                    icon: this.getPictoUrl('gite', true)
                }]
            },{
                text: OpenLayers.i18n('sites'),
                checked: false,
                layerName: 'c2corg:sites',
                id: 'sites',
                icon: this.getPictoUrl('sites')
            },{
                text: OpenLayers.i18n('users'),
                checked: false,
                layerName: 'c2corg:users',
                id: 'users',
                icon: this.getPictoUrl('users')
            },{
                text: OpenLayers.i18n('images'),
                checked: false,
                layerName: 'c2corg:images',
                id: 'images',
                icon: this.getPictoUrl('images')
            },{
                text: OpenLayers.i18n('products'),
                checked: false,
                layerName: 'c2corg:products',
                id: 'products',
                icon: this.getPictoUrl('products')
            },{
                text: OpenLayers.i18n('routes'),
                checked: false,
                layerName: 'c2corg:routes',
                id: 'routes',
                icon: this.getPictoUrl('routes')
            },{
                text: OpenLayers.i18n('outings'),
                checked: false,
                layerName: 'c2corg:outings',
                id: 'outings',
                icon: this.getPictoUrl('outings')
            },{
                text: OpenLayers.i18n('maps'),
                checked: false,
                layerName: 'c2corg:maps',
                id: 'maps',
                icon: this.getPictoUrl('maps')
            },{
                text: OpenLayers.i18n('areas'),
                icon: this.getPictoUrl('areas'),
                checked: false,
                children: [{
                    text: OpenLayers.i18n('ranges'),
                    checked: false,
                    layerName: 'c2corg:ranges',
                    id: 'ranges',
                    iconCls: 'noIconLayer'
                },{
                    text: OpenLayers.i18n('countries'),
                    checked: false,
                    layerName: 'c2corg:countries',
                    id: 'countries',
                    iconCls: 'noIconLayer'
                },{
                    text: OpenLayers.i18n('admin boundaries'),
                    checked: false,
                    layerName: 'c2corg:admin_limits',
                    id: 'admin_limits',
                    iconCls: 'noIconLayer'
                }]
            }]
        }];
    },

    getPictoUrl: function(name, alt) {
        if (alt) {
            return this.baseConfig.staticBaseUrl + '/static/images/picto/' + name + '.png';
        }
        return this.baseConfig.staticBaseUrl + '/static/images/modules/' + name + '_mini.png';
    },

    getDrawingLayer: function() {
        if (!this.drawLayer) {
           var context = { 
                getIcon: function(feature) {
                    if (feature.geometry instanceof OpenLayers.Geometry.Point) {
                        //return this.baseConfig.staticBaseUrl + "static/images/mapmarker.png"; // FIXME: "this" is not defined
                        return "/static/images/mapmarker.png";
                    }
                    return null;
                }
            };
            var myStyles = new OpenLayers.StyleMap({
                "default": new OpenLayers.Style({
                    externalGraphic: "${getIcon}",
                    graphicWidth: 32, 
                    graphicHeight: 32, 
                    graphicYOffset: -30
                }, {context: context}),
                "select": new OpenLayers.Style({
                    externalGraphic: "${getIcon}",
                    graphicWidth: 48,
                    graphicHeight: 48,
                    graphicYOffset: -45
                }, {context: context})
            });
            this.drawLayer = new OpenLayers.Layer.Vector("Drawings layer", {
                displayInLayerSwitcher: false,
                styleMap: myStyles
            });
            this.selectCtrl = new OpenLayers.Control.SelectFeature(this.drawLayer, {hover: true});
            this.map.addControl(this.selectCtrl);
            this.selectCtrl.activate();
        }
        return this.drawLayer;
    },

    /** 
     * get list of activated layers that may be queried
     */
    getEnabledQueryableLayers: function() {
        var layers = [];
        var areas_layers = [];
        var parkings_layers = [];
        var olLayers = this.map.getLayersByClass('OpenLayers.Layer.WMS');
        for (var i = 0; i < olLayers.length; ++i) {
            var cur = olLayers[i];

            if (cur.getVisibility()) {
                for (var j = 0; j < cur.params.LAYERS.length; ++j) {
                    var layername = cur.params.LAYERS[j];
                    if (['ranges', 'countries', 'admin_limits'].indexOf(layername) != -1) {
                        areas_layers.push(layername);
                    } else if (['public_transportations', 'other_access'].indexOf(layername) != -1) {
                        parkings_layers.push(layername);
                    } else {
                        layers.push(layername);
                    }
                }
            }

            // if ranges, admin_limits and countries are selected, replace it by areas
            if (areas_layers.length == 3) {
                layers.push('areas');
            } else {
                layers = layers.concat(areas_layers);
            }

            // if both public_transportations and other_access are selected, replace it by parkings
            if (parkings_layers.length == 2) {
                layers.push('parkings');
            } else {
                layers = layers.concat(parkings_layers);
            }
        }

        return layers;
    },
    
    centerOnArgParserCenter: function() {
        var center = new OpenLayers.LonLat(this.argParserCenter.lon, this.argParserCenter.lat);
        this.map.setCenter(
            center.transform(this.epsg4326, this.map.getProjection()),
            this.argParserCenter.zoom ? this.argParserCenter.zoom : null
        );
    },

    updateOpenLayersImgPath: function(isTrunk) {
        OpenLayers.ImgPath = this.baseConfig.staticBaseUrl + '/static/';
        OpenLayers.ImgPath += (isTrunk) ? 'js/mapfish/mfbase/openlayers/img/' : 'images/openlayers/';
    },
    
    addPermalinkControl: function() {
        var permalink = new c2corg.Permalink('permalink' + this.apiId,
            this.baseConfig.baseUrl + 'map', {api: this});
        permalink.activate();
        this.map.addControl(permalink);
    },
    
    setBaseLayerByName: function(layerId) {
        var layers = this.map.layers;
        for (var i = 0, len = layers.length; i < len; i++) {
            if (layers[i].name == layerId && layers[i].isBaseLayer) {
                this.map.setBaseLayer(layers[i]);
            }
        }
    },

    deactivateTooltip: function() {
        this.tooltip.deactivate();
        this.tooltipTest.deactivate();
    },

    activateTooltip: function() {
        this.tooltip.activate();
        this.tooltipTest.activate();
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
    
        if (newBaseLayer != this.baseLayer) {
     
            // ensure newBaseLayer is already loaded
            if (OpenLayers.Util.indexOf(this.layers, newBaseLayer) != -1) {

                // preserve center and scale when changing base layers
                var center = this.getCenter();
                var newResolution = OpenLayers.Util.getResolutionFromScale(
                    this.getScale(), newBaseLayer.units
                );

                // make the old base layer invisible 
                if (this.baseLayer != null && !this.allOverlays) {
                    this.baseLayer.setVisibility(false);
                }

                var oldProjection = this.getProjection();

                // set new baselayer
                this.baseLayer = newBaseLayer;
     
                // Increment viewRequestID since the baseLayer is 
                // changing. This is used by tiles to check if they should 
                // draw themselves.
                this.viewRequestID++;
                if(!this.allOverlays) {
                    this.baseLayer.setVisibility(true);
                }

                var newProjection = this.getProjection();
                var hasProjectionChanged = (oldProjection && oldProjection.projCode != newProjection.projCode);

                // recenter the map
                if (center != null) {

                     if (hasProjectionChanged) {
                         // reproject new center of the map
                         center.transform(oldProjection, this.getProjection());
                    }

                    // new zoom level derived from old scale
                    var newZoom = this.getZoomForResolution(
                        newResolution || this.resolution, true 
                    );
                    // zoom and force zoom change
                    this.setCenter(center, newZoom, false, true);
                }

                if (hasProjectionChanged) {
                    // reproject vector layers
                    this.updateVectorLayers();
                }

                this.events.triggerEvent("changebaselayer", {
                    layer: this.baseLayer
                });
            }     
        }
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

c2corg.API.GpLogo = OpenLayers.Class(Geoportal.Control.PermanentLogo, {
    api: null,
    
    initialize: function(options) {
        Geoportal.Control.PermanentLogo.prototype.initialize.apply(this, arguments);
        if (options.api) {
            this.api = options.api;
            this.permaLogo = this.api.baseConfig.staticBaseUrl + '/static/js/mapfish/geoportal/img/logo_gp.gif';
        }
    },
    
    draw: function() {
        Geoportal.Control.PermanentLogo.prototype.draw.apply(this, arguments);
        
        this.map.events.on({
            scope: this.div,
            'changebaselayer': function(evt) {
                if (evt.layer instanceof Geoportal.Layer.WMSC) {
                    this.className = "gpControlPermanentLogoActive";
                } else {
                    this.className = "gpControlPermanentLogo";
                }
            }
        });
        
        return this.div; 
    }
});
