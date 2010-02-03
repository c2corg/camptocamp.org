/**
 * @requires c2corgApi/js/c2corg_api.js
 * @requires OpenLayers/BaseTypes/Class.js
 * @requires OpenLayers/Format/GeoJSON.js
 * @requires OpenLayers/Popup/FramedCloud.js
 * @requires core/Protocol/MapFish.js
 * @requires core/Protocol/TriggerEventDecorator.js
 * @requires core/Searcher/Map.js
 */

Ext.namespace("c2corg");

c2corg.API.Tooltip = OpenLayers.Class({

    api: null,
    map: null,

    initialize: function(options) {
        this.api = options.api;
        this.map = this.api.map;

        var layers = this.api.getEnabledQueryableLayers();

        // tooltip_test setup

        this.testProtocol = new mapfish.Protocol.MapFish({
            url: this.api.baseConfig.baseUrl + 'documents/tooltipTest',
            format: new OpenLayers.Format.JSON(),
            params: {
                layers: layers
            }
        });

        this.map.events.register('mouseout', this, function() {
            var tooltip = Ext.get('tooltip_tooltip').dom;
            tooltip.style.display = "none";
        });

        // triggerEventProtocol used to be able to add the layers list into the parameter on clic and handle response
        this.testTriggerEventProtocol = new mapfish.Protocol.TriggerEventDecorator({
            protocol: this.testProtocol
        });
        // before sending query
        this.testTriggerEventProtocol.events.register('crudtriggered', this, function() {
            this.testTriggerEventProtocol.protocol.params.layers = this.api.getEnabledQueryableLayers();
        });
        // when receiving response
        this.testTriggerEventProtocol.events.register('crudfinished', this, function(response, toto, tutu) {
            var tooltip = Ext.get('tooltip_tooltip').dom;
            if (response.features && response.features.totalObjects > 0) {
                this.map.viewPortDiv.style.cursor = 'pointer';
                var px = this.map.getViewPortPxFromLonLat(
                    new OpenLayers.LonLat(response.features.lon, response.features.lat)
                );
                tooltip.innerHTML = OpenLayers.i18n('${nb_items} items. Click to show info', {
                    nb_items: response.features.totalObjects
                });
                var tooltip_top = this.map.div.offsets[1] + px.y + 10;
                var tooltip_left = this.map.div.offsets[0] + px.x + 10;
                tooltip.style.top = tooltip_top + 'px';
                tooltip.style.left = tooltip_left + 'px';
                tooltip.style.display = "block";
            } else {
                this.map.viewPortDiv.style.cursor = 'auto';
                tooltip.style.display = "none";
            }
        });

        // searcher
        this.testSearcher = new mapfish.Searcher.Map({
            map: this.map,
            mode: mapfish.Searcher.Map.HOVER,
            delay: 250,
            scope: this,
            searchTolerance: 10,
            protocol: this.testTriggerEventProtocol
        });

        this.map.addControl(this.testSearcher);
        this.testSearcher.activate();

        // tooltip setup

        this.protocol = new mapfish.Protocol.MapFish({
            url: this.api.baseConfig.baseUrl + 'documents/tooltip',
            format: new OpenLayers.Format.GeoJSON(),
            params: {
                layers: layers
            }
        });

        // event on click on map
        // triggerEventProtocol used to be able to add the layers list into the parameter on clic and handle response
        this.triggerEventProtocol = new mapfish.Protocol.TriggerEventDecorator({
            protocol: this.protocol
        });
        // before sending query
        this.triggerEventProtocol.events.register('crudtriggered', this, function(obj) {
            this.map.viewPortDiv.style.cursor =  'progress';
            
            // query only activated layers
            this.triggerEventProtocol.protocol.params.layers = this.api.getEnabledQueryableLayers();
        });
        // when receiving response
        this.triggerEventProtocol.events.register('crudfinished', this, this.onGotFeatures);

        // searcher
        this.searcher = new mapfish.Searcher.Map({
            map: this.map,
            mode: mapfish.Searcher.Map.CLICK,
            scope: this,
            searchTolerance: 10,
            projection: this.api.epsg900913,
            protocol: this.triggerEventProtocol
        });

        this.map.addControl(this.searcher);
        this.searcher.activate();
    },

    onGotFeatures: function(response) {
        this.map.viewPortDiv.style.cursor =  'auto';

        var features = response.features, len = features.length;
        if (len > 0) {
            // TODO: what if more than 1 result?

            var feature = features[0], geom = feature.geometry, popupUrl;
            
            var projection = this.map.baseLayer.CLASS_NAME == "Geoportal.Layer.WMSC" ?
                             this.api.fxx : this.api.epsg900913;
            geom = geom.transform(this.api.epsg4326, projection);
            
            popupUrl = this.api.baseConfig.baseUrl + feature.attributes.layer;
            popupUrl += '/popup/' + feature.attributes.id + '/fr'; // FIXME: if not fr?
            
            /*
            this.api.showPopup({
                easting: geom.x,
                northing: geom.y,
                width: 400,
                height: 300,
                html: '<iframe src="' + popupUrl + '" width="300" height="400"></iframe>'
            });
            */
            
            // use default OpenLayers pictos path
            this.api.updateOpenLayersImgPath(true);
            
            this.map.addPopup(new OpenLayers.Popup.FramedCloud("popup",
                new OpenLayers.LonLat(geom.x, geom.y),
                new OpenLayers.Size(400, 300),
                '<iframe src="' + popupUrl + '" width="300" height="400"></iframe>',
                null,
                true,
                null));
            
            // use customized OpenLayers pictos path
            this.api.updateOpenLayersImgPath(false);
        }
    }

});
