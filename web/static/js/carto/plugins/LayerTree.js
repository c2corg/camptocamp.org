/*
 * @requires plugins/Tool.js
 * @requires OpenLayers/Request.js
 * @include OpenLayers/Layer/Vector.js
 * @include OpenLayers/Strategy/BBOX.js
 * @include OpenLayers/Protocol/WFS/v1_0_0.js
 * @include OpenLayers/Protocol/WFS.js
 * @include OpenLayers/Control/SelectFeature.js
 * @include GeoExt/widgets/tree/LayerNode.js
 * @include styles.js
 * @include i18n.js
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

    createVectorLayer: function(options) {
        return new OpenLayers.Layer.Vector(options.name, {
            strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1, ratio: 1})],
            protocol: new OpenLayers.Protocol.WFS({
                featureType: options.featureType,
                url: this.url,
                maxFeatures: options.maxFeatures || 200,
                geometryName: "geom",
                srsName: "EPSG:900913"
            }),
            isBaseLayer: false,
            visibility: false,
            styleMap: c2corg.styleMap()
        });
    },

    addLayers: function() {
        this.layers = {
            "summits": this.createVectorLayer({name: "summits", featureType: "summits"}),
            "access": this.createVectorLayer({name: "access", featureType: "access"}),
            "huts": this.createVectorLayer({name: "huts", featureType: "huts"}),
            "sites": this.createVectorLayer({name: "sites", featureType: "sites"}),
            "users": this.createVectorLayer({name: "users", featureType: "users"}),
            "images": this.createVectorLayer({name: "images", featureType: "images"}),
            "products": this.createVectorLayer({name: "products", featureType: "products"})
        };
        for (var i in this.layers) {
            this.mapPanel.map.addLayer(this.layers[i]);
        }
    },

    getThemes: function() {
        return [{
            text: c2corg.i18n("summits"),
            nodeType: "gx_layer",
            layer: this.layers["summits"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/summits_mini.png",
            expanded: false,
            children: [{
                text: c2corg.i18n("pass"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/pass.png",
                leaf: true
            },{
                text: c2corg.i18n("lake"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/lake.png",
                leaf: true
            },{
                text: c2corg.i18n("valley"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/crag.png",
                leaf: true
            }]  
        }, {
            text: c2corg.i18n("parkings"),
            nodeType: "gx_layer",
            layer: this.layers["access"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/parkings_mini.png",
            expanded: false,
            children: [{
                text: c2corg.i18n("public_transportations"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/parking_green.png",
                leaf: true
            }]
        }, {
            text: c2corg.i18n("huts"),
            nodeType: "gx_layer",
            layer: this.layers["huts"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/huts_mini.png",
            expanded: false,
            children: [{
                text: c2corg.i18n("gite"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/gite.png",
                leaf: true
            }, {
                text: c2corg.i18n("camping area"),
                icon: c2corg.config.staticBaseUrl + "/static/images/picto/camp.png",
                leaf: true
            }]
        }, {
            text: c2corg.i18n("sites"),
            nodeType: "gx_layer",
            layer: this.layers["sites"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/sites_mini.png",
            leaf: true
        }, {
            text: c2corg.i18n("users"),
            nodeType: "gx_layer",
            layer: this.layers["users"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/users_mini.png",
            leaf: true
        }, {
            text: c2corg.i18n("images"),
            nodeType: "gx_layer",
            layer: this.layers["images"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/images_mini.png",
            leaf: true
        }, {
            text: c2corg.i18n("products"),
            nodeType: "gx_layer",
            layer: this.layers["products"],
            icon: c2corg.config.staticBaseUrl + "/static/images/modules/products_mini.png",
            leaf: true
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
        var layers = [];
        for (var name in this.layers) {
            layers.push(this.layers[name]);
        }
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
