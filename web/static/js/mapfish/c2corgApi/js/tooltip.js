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
        var feature;
        for (var fid in features) {
            // TODO: what if more than 1 result?
            feature = features[fid];
            break;
        }

        /*
        var geom = feature.geometry;
        var projection = this.map.baseLayer instanceof Geoportal.Layer.WMSC ?
                         this.api.fxx : this.api.epsg900913;
        geom = geom.transform(this.api.epsg4326, projection);
        */

        var popupUrl = this.api.baseConfig.baseUrl + feature.attributes.layer;
        popupUrl += '/popup/' + feature.attributes.id + '/fr'; // FIXME: if not fr?

        /*
        this.api.showPopup({
            easting: geom.x,
            northing: geom.y,
            width: 300,
            height: 400,
            html: '<iframe src="' + popupUrl + '" width="300" height="400"></iframe>'
        });
        */

        // use default OpenLayers pictos path
        this.api.updateOpenLayersImgPath(true);

        this.map.addPopup(new OpenLayers.Popup.FramedCloud("popup",
            this.clickLonLat, //new OpenLayers.LonLat(geom.x, geom.y),
            new OpenLayers.Size(300, 400),
            '<iframe src="' + popupUrl + '" class="tooltip_iframe"></iframe>',
            null,
            true,
            null));

        // use customized OpenLayers pictos path
        this.api.updateOpenLayersImgPath(false);
    }
});
