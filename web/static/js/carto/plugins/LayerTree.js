/*
 * @requires plugins/Tool.js
 * @requires OpenLayers/Request.js
 * @include OpenLayers/Layer/Vector.js
 * @include OpenLayers/Strategy/BBOX.js
 * @include OpenLayers/Protocol/WFS/v1_0_0.js
 * @include OpenLayers/Protocol/WFS.js
 * @include OpenLayers/Control/SelectFeature.js
 * @include GeoExt/widgets/tree/LayerNode.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.LayerTree = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_layertree",

    init: function() {
        c2corg.plugins.LayerTree.superclass.init.apply(this, arguments);
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

        this.tree = c2corg.plugins.LayerTree.superclass.addOutput.call(this, config);
        this.tree.findParentByType('window').alignTo(this.target.mapPanel.getEl(), "tr-tr", [-20, 45]);
        return this.tree;
    }
});

Ext.preg(c2corg.plugins.LayerTree.prototype.ptype, c2corg.plugins.LayerTree);

// FIXME: remove hard-coded base URL for static images

Ext.namespace("c2corg.tree");

c2corg.tree.LayerTree = Ext.extend(Ext.tree.TreePanel, {

    baseCls: 'layertree',
    enableDD: false,
    rootVisible: false,
    useArrows: true,

    mapPanel: null,
    initialState: null,
    initialThemes: null,
    
    stateEvents: ["layervisibilitychange"],
    stateId: 'tree',

    url: null,
    layers: {},

    initComponent: function() {
        this.addLayers();
        this.root = {
            children: this.getThemes()
        };
        c2corg.tree.LayerTree.superclass.initComponent.call(this, arguments);

        this.addEvents(
            /** private: event[layervisibilitychange]
             *  Fires after a checkbox state changes
             */
            "layervisibilitychange"
        );
        this.on('checkchange', function(node, checked) {
            this.fireEvent("layervisibilitychange");
        }, this);
    },

    addLayers: function() {

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

        this.layers = {
            "summits": new OpenLayers.Layer.Vector("summits", {
                strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1, ratio: 1})],
                protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                    featureType: 'summits'
                }, WFS_PROTOCOL_OPTIONS)),
                isBaseLayer: false,
                visibility: false,
                styleMap: styleMap
            }),
            "access": new OpenLayers.Layer.Vector("access", {
                strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1, ratio: 1})],
                protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                    featureType: 'access'
                }, WFS_PROTOCOL_OPTIONS)),
                isBaseLayer: false,
                visibility: false,
                styleMap: styleMap
            }),
            "huts": new OpenLayers.Layer.Vector("huts", {
                strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1, ratio: 1})],
                protocol: new OpenLayers.Protocol.WFS(Ext.apply({
                    featureType: 'huts'
                }, WFS_PROTOCOL_OPTIONS)),
                isBaseLayer: false,
                visibility: false,
                styleMap: styleMap
            })
        };
        for (var i in this.layers) {
            this.mapPanel.map.addLayer(this.layers[i]);
        }
    },

    getThemes: function() {
        return [{
            text: "Sommets",
            nodeType: "gx_layer",
            layer: this.layers["summits"],
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
            layer: this.layers["access"],
            icon: "http://s.camptocamp.org/static/images/modules/parkings_mini.png",
            leaf: true
        }, {
            text: "Refuges",
            nodeType: "gx_layer",
            layer: this.layers["huts"],
            icon: "http://s.camptocamp.org/static/images/modules/huts_mini.png",
            expanded: false,
            children: [{
                text: 'camp',
                leaf: true
            }, {
                text: 'bivouac',
                leaf: true
            }]
        }];
    },
    
    /** api: method[applyState]
     *  :arg state: ``Object``
     */
    applyState: function(state) {
        // actual state is loaded later in delayedApplyState to prevent
        // the layer from being displayed under the baselayers
        this.initialState = state;
    },   

    /** private: method[delayedApplyState]
     */
    delayedApplyState: function() {
        if (this.initialState && this.initialState.layers) {
            this.initialThemes = Ext.isArray(this.initialState.layers)
                                 ? this.initialState.layers
                                 : [this.initialState.layers];
        }
    },

    getState: function() {
        var layers = [], state = {};
        for (var i in this.layers) {
            if (this.layers[i].getVisibility()) {
                layers.push(i);
            }
        }
        if (layers) {
            state['layers'] = layers.join(",");
        }
        return state;
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

    makeThemesInteractive: function() {
        var layers = [this.layers["summits"], this.layers["access"], this.layers["huts"]];
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
                    var popupUrl = '/' + feature.data.module + '/popup/' + feature.data.id + '/raw/true';
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
