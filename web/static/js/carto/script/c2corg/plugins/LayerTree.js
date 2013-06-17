/*
 * @requires plugins/Tool.js
 * @requires OpenLayers/Request.js
 * @include OpenLayers/Layer/Vector.js
 * @include OpenLayers/Strategy/BBOX.js
 * @include OpenLayers/Protocol/WFS/v1_0_0.js
 * @include OpenLayers/Protocol/WFS.js
 * @include OpenLayers/Protocol/HTTP.js
 * @include OpenLayers/Format/JSON.js
 * @include OpenLayers/Control/SelectFeature.js
 * @include GeoExt/widgets/tree/LayerNode.js
 * @include c2corg/config/styles.js
 */

Ext.namespace("c2corg.plugins");

c2corg.plugins.LayerTree = Ext.extend(gxp.plugins.Tool, {

    ptype: "c2corg_layertree",

    init: function() {
        c2corg.plugins.LayerTree.superclass.init.apply(this, arguments);
        this.target.on("ready", this.viewerReady, this);
    },

    viewerReady: function() {
        var w = this.tree.findParentByType("window");
        w.el.show(); // we don't use w.show() since it would grab focus

        this.tree.delayedApplyState();
        this.tree.loadInitialThemes();
        this.tree.makeThemesInteractive();

        // listen on window resize to be sure that the
        // c2c layers window won't go out of the map
        Ext.EventManager.onWindowResize(function() {
            var m = this.target.mapPanel.getEl();
            var xy = w.el.getAlignToXY(m, "tr-tr", [-5, 0]);
            var pos = w.getPosition();

            if (xy[0] <= pos[0]) {
                w.alignTo(m, "tr-tr", [-20, pos[1]-xy[1]]);
            }
        }, this);
    },

    addOutput: function(config) {

        config = Ext.apply({
            xtype: "c2corg_layertree",
            mapPanel: this.target.mapPanel,
            url: this.url,
            initialThemes: this.initialThemes || []
        }, config || {});

        this.tree = c2corg.plugins.LayerTree.superclass.addOutput.call(this, config);
        this.tree.findParentByType("window").hide().alignTo(this.target.mapPanel.getEl(), "tr-tr", [-20, 45]);
        return this.tree;
    }
});

Ext.preg(c2corg.plugins.LayerTree.prototype.ptype, c2corg.plugins.LayerTree);

Ext.namespace("c2corg.tree");

c2corg.tree.LayerTree = Ext.extend(Ext.tree.TreePanel, {

    baseCls: "layertree",
    enableDD: false,
    rootVisible: false,
    useArrows: true,

    mapPanel: null,
    initialState: null,
    initialThemes: null,
    
    stateEvents: ["layervisibilitychange"],
    stateId: "tree",

    url: null,
    layers: {},
    popups: [],
    styleMap: null,

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
        this.on("checkchange", function(node, checked) {
            this.fireEvent("layervisibilitychange");
        }, this);
    },

    getStyleMap: function() {
        if (!this.styleMap) {
            this.styleMap = c2corg.styleMap('c2clayer');
        }
        return this.styleMap;
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
            styleMap: this.getStyleMap()
        });
    },

    addLayers: function() {
        this.layers = {
            "summits": this.createVectorLayer({name: "summits", featureType: "summits"}),
            "access": this.createVectorLayer({name: "access", featureType: "access"}),
            "public_transportations": this.createVectorLayer({name: "public_transportations", featureType: "public_transportations"}),
            "huts": this.createVectorLayer({name: "huts", featureType: "huts"}),
            "sites": this.createVectorLayer({name: "sites", featureType: "sites"}),
            "users": this.createVectorLayer({name: "users", featureType: "users"}),
            "images": this.createVectorLayer({name: "images", featureType: "images"}),
            "products": this.createVectorLayer({name: "products", featureType: "products"}),
            "routes": this.createVectorLayer({name: "routes", featureType: "routes", maxFeatures: 50}),
            "outings": this.createVectorLayer({name: "outings", featureType: "outings", maxFeatures: 50}),
            "maps": this.createVectorLayer({name: "maps", featureType: "maps"}),
            "ranges": this.createVectorLayer({name: "ranges", featureType: "ranges"}),
            "admin_limits": this.createVectorLayer({name: "admin_limits", featureType: "admin_limits"}),
            "countries": this.createVectorLayer({name: "countries", featureType: "countries"})
        };
        for (var i in this.layers) {
            this.mapPanel.map.addLayer(this.layers[i]);
        }
    },

    getThemes: function() {
        return [{
            text: OpenLayers.i18n("summits"),
            nodeType: "gx_layer",
            layer: this.layers["summits"],
            iconCls: "picto_summits",
            expanded: false,
            children: [{
                text: OpenLayers.i18n("pass"),
                iconCls: "picto_pass",
                leaf: true
            },{
                text: OpenLayers.i18n("lake"),
                iconCls: "picto_lake",
                leaf: true
            },{
                text: OpenLayers.i18n("valley"),
                iconCls: "picto_crag",
                leaf: true
            }]  
        }, {
            text: OpenLayers.i18n("parkings"),
            nodeType: "gx_layer",
            layer: this.layers["access"],
            iconCls: "picto_parkings",
            expanded: false,
            children: [{
                text: OpenLayers.i18n("public_transportations"),
                iconCls: "picto_parking_green",
                leaf: true
            }]
        }, {
            text: OpenLayers.i18n("huts"),
            nodeType: "gx_layer",
            layer: this.layers["huts"],
            iconCls: "picto_huts",
            expanded: false,
            children: [{
                text: OpenLayers.i18n("gite"),
                iconCls: "picto_gite",
                leaf: true
            }, {
                text: OpenLayers.i18n("camping area"),
                iconCls: "picto_camp",
                leaf: true
            }]
        }, {
            text: OpenLayers.i18n("sites"),
            nodeType: "gx_layer",
            layer: this.layers["sites"],
            iconCls: "picto_sites",
            leaf: true
        }, {
            text: OpenLayers.i18n("routes"),
            nodeType: "gx_layer",
            layer: this.layers["routes"],
            iconCls: "picto_routes",
            leaf: true
        }, {
            text: OpenLayers.i18n("More..."),
            expanded: false,
            children: [{
                text: OpenLayers.i18n("users"),
                nodeType: "gx_layer",
                layer: this.layers["users"],
                iconCls: "picto_users",
                leaf: true
            }, {
                text: OpenLayers.i18n("images"),
                nodeType: "gx_layer",
                layer: this.layers["images"],
                iconCls: "picto_images",
                leaf: true
            }, {
                text: OpenLayers.i18n("products"),
                nodeType: "gx_layer",
                layer: this.layers["products"],
                iconCls: "picto_products",
                leaf: true
            }, {
                text: OpenLayers.i18n("outings"),
                nodeType: "gx_layer",
                layer: this.layers["outings"],
                iconCls: "picto_outings",
                leaf: true
            }, {
                text: OpenLayers.i18n("public_transportations"),
                nodeType: "gx_layer",
                layer: this.layers["public_transportations"],
                iconCls: "picto_parking_green",
                leaf: true
            }, {
                text: OpenLayers.i18n("maps"),
                nodeType: "gx_layer",
                layer: this.layers["maps"],
                iconCls: "picto_maps",
                leaf: true
            }, {
                text: OpenLayers.i18n("areas"),
                expanded: false,
                iconCls: "picto_areas",
                children: [{
                    text: OpenLayers.i18n("ranges"),
                    nodeType: "gx_layer",
                    layer: this.layers["ranges"],
                    iconCls: "picto_blank",
                    leaf: true
                }, {
                    text: OpenLayers.i18n("admin boundaries"),
                    nodeType: "gx_layer",
                    layer: this.layers["admin_limits"],
                    iconCls: "picto_blank",
                    leaf: true
                }, {
                    text: OpenLayers.i18n("countries"),
                    nodeType: "gx_layer",
                    layer: this.layers["countries"],
                    iconCls: "picto_blank",
                    leaf: true
                }]
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
            state["layers"] = layers.join(",");
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

        var clickControl = new OpenLayers.Control.SelectFeature(
            layers, {
                clickout: true,
                onSelect: function(feature) {

                    // close existing pinned popups
                    for (var i = 0; i < this.popups.length; i++) {
                      if (!this.popups[i].body.dom) { // clean closed popups
                        this.popups.splice(i, 1);
                        i--;
                      } else if (!this.popups[i].draggable) { // draggable popups are unpinned
                        this.popups[i].close();
                        this.popups.splice(i, 1);
                        i--;
                      }
                    }

                    var popup = new GeoExt.Popup({
                        width: 440,
                        height: 200,
                        autoScroll: true,
                        resizable: false,
                        cls: "popup_content",
                        location: feature
                    });
                    popup.show();
                    var popupUrl = "/" + feature.data.module + "/popup/" + feature.data.id + "/raw/true";
                    popup.load({
                        url: popupUrl,
                        timeout: 60,
                        text: OpenLayers.i18n("Please wait..."),
                        scripts: true
                    });

                    this.popups.push(popup);
                },
                scope: this
            });

        var hoverControl = new c2corg.control.hoverFeature(layers);
 
        this.mapPanel.map.addControl(hoverControl);
        this.mapPanel.map.addControl(clickControl);

        // FIXME we only activate the controls when at least on layer is active
        // This beacause we want a hoverControl for features displayed on the embedded map (see ShowFeatures.js)
        // Unfortunately we cannot add controls here and there on different sets of layers, only the last defined one
        // will get the events. (that's also why ShowFeatures plugin is called first in embedded.js)
        // This is the easiest solution for now, the drawback being that you cannot get the hover effect on features if you
        // have activated on of the c2c objetcs layers.
        // An other solution could be to make this plugin aware of the feature layer from ShowFeature plugin, have only one other control
        // on all those layers and distinguish afterwards, or even merge the two plugins together, but this is more changes and quite dirty
        this.on("layervisibilitychange", function() {
            if (this.getChecked().length) {
                hoverControl.activate();
                clickControl.activate();
            } else {
                hoverControl.deactivate();
                clickControl.deactivate();
            }
        }, this);
    }
});

Ext.reg("c2corg_layertree", c2corg.tree.LayerTree);

Ext.namespace("c2corg.control");

c2corg.control.hoverFeature = OpenLayers.Class(OpenLayers.Control.SelectFeature, {
    hover: true,
    highlightOnly: true,
    renderIntent: "temporary",
    protocol: null,
    listening: true,

    initialize: function(layers, options) {
        OpenLayers.Control.SelectFeature.prototype.initialize.apply(this, [layers, options]);

        this.protocol = new OpenLayers.Protocol.HTTP({
            url: "/documents/tooltipPreview",
            format: new OpenLayers.Format.JSON(),
            params: {}
        }); 

        this.events.on({
            //beforefeaturehighlighted: this.report,
            featurehighlighted: this.showPreview,
            featureunhighlighted: this.hidePreview,
            scope: this
        });
    },
    
    showPreview: function(e) {
        if (!this.listening) {
            return;
        }
        this.listening = false;
        var feature = e.feature;
        this.currentFeature = feature;
        this.protocol.read({
            params: {
                module: feature.data.module,
                id: feature.data.id
            },
            callback: function(result) {
                if (result.success()) {
                    if (this.currentFeature.geometry instanceof OpenLayers.Geometry.Point) {
                        var lonlat = new OpenLayers.LonLat(this.currentFeature.geometry.x,
                                                           this.currentFeature.geometry.y);
                    } else {
                        // FIXME: would be better to use the cursor position
                        var lonlat = this.currentFeature.bounds.getCenterLonLat();
                    }
                    var px = this.map.getViewPortPxFromLonLat(lonlat);
                    this.div.innerHTML = OpenLayers.i18n("${item}. Click to show info", {
                        item: result.features.name
                    });
                    this.div.style.top = (px.y + 10) + "px";
                    this.div.style.left = (px.x + 10) + "px";
                    this.div.style.display = "block";
                }
            },
            scope: this
        });
    },

    hidePreview: function(e) {
        this.div.innerHTML = "";
        this.div.style.display = "none";
        this.listening = true;
    },

    draw: function() {
        OpenLayers.Control.prototype.draw.apply(this, arguments);
        this.div.className = "tooltip_tooltip";
        this.div.style.display = "none";
        return this.div;
    }
});
