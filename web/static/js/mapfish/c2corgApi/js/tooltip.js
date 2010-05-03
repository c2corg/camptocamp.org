/**
 * @requires OpenLayers/Control/GetFeature.js
 * @include OpenLayers/BaseTypes/Element.js
 * @include OpenLayers/Format/GeoJSON.js
 * @include OpenLayers/Lang.js
 * @include OpenLayers/Popup/FramedCloud.js
 * @include OpenLayers/Protocol/HTTP.js
 * @include OpenLayers/Util.js
 * @include geoportal/GeoportalMin.js
 */

Ext.namespace("c2corg.API");

c2corg.API.TooltipTest = OpenLayers.Class(OpenLayers.Control.GetFeature, {

    api: null,
    hoverLonLat: null,
    clickTolerance: 20,
    click: false,
    hover: true,
    mustSleep: null,

    initialize: function(options) {
        options = options || {}; 

        this.api = options.api;

        OpenLayers.Util.extend(options, {
            protocol: new OpenLayers.Protocol.HTTP({
                url: this.api.baseConfig.baseUrl + 'documents/tooltipTest',
                format: new OpenLayers.Format.JSON(),
                params: {}
            })
        });
        OpenLayers.Control.GetFeature.prototype.initialize.apply(this, [options]);

        this.map.events.register('mouseout', this, function() {
            this.div.style.display = "none";
        });
        this.map.events.register('click', this, function() {
            if (this.map.viewPortDiv.style.cursor == 'pointer') {
                this.map.viewPortDiv.style.cursor = 'auto';
            }
            this.div.style.display = "none";
        });
        this.map.events.register('movestart', this, this.deactivate);
        this.map.events.register('moveend', this, this.activate);
        // TODO: neutralize tooltipTest when zoomBoxing or when measure tool is on
    },

    activate: function() {
        if (this.mustSleep) return false;
        return OpenLayers.Control.GetFeature.prototype.activate.call(this);
    },

    request: function(bounds, options) {
        options = options || {}; 
        
        if (this.map.baseLayer instanceof Geoportal.Layer.WMSC) {
            bounds = bounds.transform(this.api.fxx, this.api.epsg900913);
        }

        var filter = new OpenLayers.Filter.Spatial({
            type: this.filterType, 
            value: bounds
        });
    
        OpenLayers.Util.extend(this.protocol.params, {
            layers: this.api.getEnabledQueryableLayers()
        });

        if (this.protocol.params.layers.length > 0) {
            var response = this.protocol.read({
                maxFeatures: options.single == true ? this.maxFeatures : undefined,
                filter: filter,
                callback: function(result) {
                    if(result.success()) {
                        this.show(result.features);
                    }
                    // Reset the cursor.
                    OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
                },
                scope: this
            });
            if(options.hover == true) {
                this.hoverResponse = response;
            }
        } else {
            OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
        }
    },

    selectHover: function(evt) {
        OpenLayers.Control.GetFeature.prototype.selectHover.apply(this, arguments);

        // record the click position
        this.hoverLonLat = this.map.getLonLatFromPixel(evt.xy);
    },

    draw: function() {
        OpenLayers.Control.prototype.draw.apply(this, arguments);
        this.div.id = "tooltip_tooltip";
        this.div.style.display = "none";
        return this.div;
    },

    show: function(data) {
        if (data && data.totalObjects > 0) {
            this.map.viewPortDiv.style.cursor = 'pointer';
            var px = this.map.getViewPortPxFromLonLat(this.hoverLonLat);
            this.div.innerHTML = OpenLayers.i18n('${nb_items} items. Click to show info', {
                nb_items: data.totalObjects
            });
            this.div.style.top = (px.y + 10) + 'px';
            this.div.style.left = (px.x + 10) + 'px';
            this.div.style.display = "block";
        } else {
            this.map.viewPortDiv.style.cursor = 'auto';
            this.div.style.display = "none";
        }
    }
})

c2corg.API.Tooltip = OpenLayers.Class(OpenLayers.Control.GetFeature, {

    api: null,
    clickLonLat: null,
    clickTolerance: 20,
    pfeatures: null,
    ppanel: null,
    currentFeatureRank: null,

    initialize: function(options) {
        options = options || {}; 

        this.api = options.api;

        OpenLayers.Util.extend(options, {
            protocol: new OpenLayers.Protocol.HTTP({
                url: this.api.baseConfig.baseUrl + 'documents/tooltip',
                format: new OpenLayers.Format.GeoJSON(),
                params: {
                    lang: OpenLayers.Lang.getCode()
                }
            })
        });
        OpenLayers.Control.GetFeature.prototype.initialize.apply(this, [options]);
    },

    request: function(bounds, options) {
        options = options || {};
        OpenLayers.Util.extend(options, {
            single: false
        });

        if (this.map.baseLayer instanceof Geoportal.Layer.WMSC) {
            bounds = bounds.transform(this.api.fxx, this.api.epsg900913);
        }

        OpenLayers.Util.extend(this.protocol.params, {
            layers: this.api.getEnabledQueryableLayers()
        });

        if (this.protocol.params.layers.length > 0) {
            OpenLayers.Control.GetFeature.prototype.request.apply(this, [bounds, options]);
        } else {
            // Reset the cursor.
            OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
        }
    },

    select: function(features) {
        OpenLayers.Control.GetFeature.prototype.select.apply(this, arguments);
        this.show(this.features);
    },

    selectSingle: function(evt) {
        OpenLayers.Control.GetFeature.prototype.selectSingle.apply(this, arguments);

        // record the click position
        this.clickLonLat = this.map.getLonLatFromPixel(evt.xy);
    },

    show: function(features) {
        this.pfeatures = [];
        this.currentFeatureRank = 0;
        
        for (var fid in features) {
            this.pfeatures.push(features[fid]);
        }
        
        // use default OpenLayers pictos path
        this.api.updateOpenLayersImgPath(true);
               
        this.map.addPopup(new OpenLayers.Popup.FramedCloud("popup",
            this.clickLonLat,
            new OpenLayers.Size(400, 200),
            '<div id="popup_content"></div>',
            null,
            true,
            null),
        true);
        
        var toolbar = null;
        if (this.pfeatures.length > 1) {
            // FIXME: when buttons are pressed they shouldn't look pressed!?
            toolbar = new Ext.Toolbar({
                border: false,
                items: [{
                        text: '<<',
                        tooltip: OpenLayers.i18n('See previous item'),
                        disabled: true,
                        id: 'popup_content_prev',
                        handler: function() {
                            if (this.currentFeatureRank == 0) return;
                            this.currentFeatureRank--;
                            this.retrievePopupContent();
                            if (this.currentFeatureRank == 0) {
                                Ext.getCmp('popup_content_prev').disable();
                            }
                            Ext.getCmp('popup_content_next').enable();
                        },
                        scope: this
                    },
                    '->',
                    {
                        text: '>>',
                        tooltip: OpenLayers.i18n('See next item'),
                        id: 'popup_content_next',
                        handler: function() {
                            if (this.currentFeatureRank == this.pfeatures.length - 1) return;
                            this.currentFeatureRank++;
                            this.retrievePopupContent();
                            Ext.getCmp('popup_content_prev').enable();
                            if (this.currentFeatureRank == this.pfeatures.length - 1) {
                                Ext.getCmp('popup_content_next').disable();
                            }
                        },
                        scope: this
                }]
            });
        }
        
        this.ppanel = new Ext.Panel({
            tbar: toolbar,
            width: 400,
            height: 200,
            autoScroll: true,
            border: false
        });
        this.ppanel.render("popup_content");
        
        // use customized OpenLayers pictos path
        this.api.updateOpenLayersImgPath(false);
        
        this.retrievePopupContent();
    },

    retrievePopupContent: function() {
        if (!this.pfeatures) return;
        this.currentFeatureRank = this.currentFeatureRank || 0;
        
        var feature = this.pfeatures[this.currentFeatureRank];
        var popupUrl = this.api.baseConfig.baseUrl + feature.attributes.layer;
        popupUrl += '/popup/' + feature.attributes.id + '/fr?raw=true'; // FIXME: if not fr?
        
        this.ppanel.load({
            url: popupUrl,
            timeout: 60,
            nocache: false,
            text: OpenLayers.i18n('Please wait...')
        });
        
        // TODO: cache result to avoid retrieving them again if already shown before?
    } 
});
