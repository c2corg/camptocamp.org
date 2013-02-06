Ext.namespace("c2corg.plugins");

c2corg.plugins.LayerTree = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_layertree",

    init: function() {
        cgxp.plugins.LayerTree.superclass.init.apply(this, arguments);
        this.target.on('ready', this.viewerReady, this);
    },

    viewerReady: function() {
        this.tree.delayedApplyState();
        this.tree.loadInitialThemes();
        this.tree.makeThemesInteractive();
    },

    addOutput: function(config) {

        config = Ext.apply({
            xtype: "c2corg_layertree",
            mapPanel: this.target.mapPanel,
            url: this.url,
            initialThemes: this.initialThemes || []
        }, config || {});

        this.tree = cgxp.plugins.LayerTree.superclass.addOutput.call(this, config);
        return this.tree;
    }
});

Ext.preg(c2corg.plugins.LayerTree.prototype.ptype, c2corg.plugins.LayerTree);


Ext.namespace("c2corg.tree");

c2corg.tree.LayerTree = Ext.extend(Ext.tree.TreePanel, {

    baseCls: 'layertree',
    enableDD: false,
    rootVisible: false,
    useArrows: true,

    mapPanel: null,
    initialThemes: null,
    url: null,

    stateEvents: ["layervisibilitychange"],
    stateId: 'tree',

    initComponent: function() {
        
        var layerNodeUI = Ext.extend(GeoExt.tree.LayerNodeUI, new GeoExt.tree.TreeNodeUIEventMixin());
        this.loader = new Ext.tree.TreeLoader({
                          uiProviders: {
                              layer: layerNodeUI,
                          }
                      });
        this.root = {
            children: this.getThemes()
        };
        c2corg.tree.LayerTree.superclass.initComponent.call(this, arguments);
    },

    getLayerStore: function() {
        if (this.layerStore) {
            return this.layerStore;
        }

        var WFS_STRATEGY = new OpenLayers.Strategy.BBOX({
            resFactor: 1,
            ratio: 1
        }); 
        var WFS_PROTOCOL_OPTIONS = { 
            url: this.url,
            maxFeatures: 200,
            geometryName: "geom",
            srsName: "EPSG:900913"
        };

        var context = {
            getIcon: function(feature) {
                if (feature.geometry instanceof OpenLayers.Geometry.Point) {
                    var attr = feature.attributes;
                    if (attr.module == "summits") {
                        if (attr.summit_type == 1) {
                            return "http://s.camptocamp.org/static/images/modules/summits_mini.png";
                        }
                        if (attr.summit_type == 2) {
                            return "http://s.camptocamp.org/static/images/picto/pass.png";
                        }
                        if (attr.summit_type == 3) {
                            return "http://s.camptocamp.org/static/images/picto/lake.png";
                        }
                        if (attr.summit_type == 4) {
                            return "http://s.camptocamp.org/static/images/picto/crag.png";
                        }
                        return "http://s.camptocamp.org/static/images/modules/summits_mini.png";
                    }
                    if (attr.module == "parkings") {
                        return "http://s.camptocamp.org/static/images/modules/parkings_mini.png";
                    }
                    if (attr.module == "huts") {
                        return "http://s.camptocamp.org/static/images/modules/huts_mini.png";
                    }
                    return null;
                }
                return null;
            }
        };

        // TODO: be able to pass styles in the plugin's config
        var styleMap = new OpenLayers.StyleMap({
            "default": new OpenLayers.Style({
                externalGraphic: "${getIcon}", // TODO: directly use a ${picto} from WFS response
                cursor: 'pointer',
                graphicWidth: 16,
                graphicHeight: 16,
                //graphicYOffset: -8,
                //graphicXOffset: -8
            }, {context: context}),
            "select": new OpenLayers.Style({
                //externalGraphic: "${getIcon}",
                //graphicWidth: 16,
                //graphicHeight: 16,
                //graphicYOffset: 0
            }, {context: context})
        });

        this.summits = new OpenLayers.Layer.Vector("summits", {
                    strategies: [WFS_STRATEGY],
                    protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                        featureType: 'summits'
                    }, WFS_PROTOCOL_OPTIONS)),
                    isBaseLayer: false,
                    visibility: false,
                    styleMap: styleMap
                });
        this.access = new OpenLayers.Layer.Vector("access", {
                    strategies: [WFS_STRATEGY],
                    protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                        featureType: 'access'
                    }, WFS_PROTOCOL_OPTIONS)),
                    isBaseLayer: false,
                    visibility: false,
                    styleMap: styleMap
                });
        this.huts = new OpenLayers.Layer.Vector("huts", {
                    strategies: [WFS_STRATEGY],
                    protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                        featureType: 'huts'
                    }, WFS_PROTOCOL_OPTIONS)),
                    isBaseLayer: false,
                    visibility: false,
                    styleMap: styleMap
                });

        this.layerStore = new GeoExt.data.LayerStore({
            map: this.mapPanel.map,
            layers: [ this.summits, this.access, this.huts ]
        });
        return this.layerStore;
    },

    getThemes: function() {
        return [{
            text: "Sommets",
            nodeType: "gx_layer",
            layerStore: this.getLayerStore(),
            layer: "summits",
            icon: "http://s.camptocamp.org/static/images/modules/summits_mini.png",
            expanded: false,
            children: [{
                text: 'col',
                icon: "http://s.camptocamp.org/static/images/picto/pass.png",
                leaf: true
            },{
                text: 'lac',
                icon: "http://s.camptocamp.org/static/images/picto/lake.png",
                leaf: true
            },{
                text: 'vallon',
                icon: "http://s.camptocamp.org/static/images/picto/crag.png",
                leaf: true
            }]  
        }, {
            text: "Accès",
            nodeType: "gx_layer",
            layerStore: this.getLayerStore(),
            layer: "access",
            icon: "http://s.camptocamp.org/static/images/modules/parkings_mini.png",
            leaf: true
        }, {
            text: "Refuges",
            nodeType: "gx_layer",
            layerStore: this.getLayerStore(),
            layer: "huts",
            icon: "http://s.camptocamp.org/static/images/modules/huts_mini.png",
            leaf: true
        }];
    },
    
    delayedApplyState: function() {
      // TODO
    },

    loadInitialThemes: function() {
        var layers, map = this.mapPanel.map;
        for (var i = 0, len = this.initialThemes.length; i < len; i++) {
            layers = map.getLayersByName(this.initialThemes[i]);
            if (layers.length == 1) {
                layers[0].setVisibility(true);
            }
        }
    },

    // TODO: move in a dedicated plugin?
    makeThemesInteractive: function() {
        var layers = [this.summits, this.access, this.huts];
        var selectControl = new OpenLayers.Control.SelectFeature(
            layers, {
                multiple: false,
                onSelect: function(feature) {
                    var popup = new GeoExt.Popup({
                        width: 440,
                        height: 200,
                        autoScroll: true,
                        cls: 'popup_content',
                        location: feature
                    });
                    popup.show();
                    var popupUrl = feature.data.module + '/popup/' + feature.data.id + '/raw/true';
                    popup.load({
                        url: popupUrl,
                        timeout: 60,
                        text: OpenLayers.i18n('Please wait...')
                    });
                },
                scope: this
            });
        this.mapPanel.map.addControl(selectControl);
        selectControl.activate();

    }
});

Ext.reg('c2corg_layertree', c2corg.tree.LayerTree);
