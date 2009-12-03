Ext.namespace("c2corg");

c2corg.API.Tooltip = OpenLayers.Class({

    api: null,
    map: null,

    initialize: function(options) {
        this.api = options.api;
        this.map = this.api.map;

        var layers = this.api.getEnabledQueryableLayers();

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
        this.triggerEventProtocol.events.register('crudtriggered', this, function() {
            // query only activated layers
            this.triggerEventProtocol.protocol.params.layers = this.api.getEnabledQueryableLayers();
            this.map.viewPortDiv.style.cursor =  'progress';
        });
        // when receiving response
        this.triggerEventProtocol.events.register('crudfinished', this, this.onGotFeatures);

        // searcher
        this.searcher = new mapfish.Searcher.Map({
            map: this.map,
            mode: mapfish.Searcher.Map.CLICK,
            scope: this,
            searchTolerance: 10,
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
            geom = geom.transform(this.api.epsg4326, this.api.epsg900913);
            popupUrl = this.api.baseConfig.baseUrl + feature.attributes.layer;
            popupUrl += '/popup/' + feature.attributes.id + '/fr'; // FIXME: if not fr?
            this.api.showPopup({
                easting: geom.x,
                northing: geom.y,
                width: 400,
                height: 300,
                html: '<iframe src="' + popupUrl + '" width="300" height="400"></iframe>'
            });
        }
    }

});
