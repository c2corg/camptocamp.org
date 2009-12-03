Ext.namespace("c2corg");

// TODO: use MapFish.API.Search?

c2corg.Query = OpenLayers.Class({

    api: null,
    map: null,
    control: null,
    summitGrid: null,
    mask: null,
    triggerEventProtocol: null,

    initialize: function(options) {

        this.api = options.api;
        this.map = this.api.map;

        var protocol = new mapfish.Protocol.MapFish({
            url: this.api.baseConfig.baseUrl + 'summits/geojson', // FIXME
            format: new OpenLayers.Format.JSON(),
            params: {
                //layers: this.api.getEnabledQueryableLayers
            }
        });

        // triggerEventProtocol used to be able to add the layers list into the parameter on clic and handle response
        this.triggerEventProtocol = new mapfish.Protocol.TriggerEventDecorator({
            protocol: protocol
        });

        // before sending query
        this.triggerEventProtocol.events.register('crudtriggered', this, function(prop) {
            //console.log(prop);
            //console.log(this.triggerEventProtocol);
            // FIXME: reproject bbox if using IGN backgrounds
            // FIXME: update AJAX url depending on queried layer
            //triggerEventProtocol.protocol.params.layers = this.api.getEnabledQueryableLayers();
            this.mask = new Ext.LoadMask(Ext.get('payload'), {msg: OpenLayers.i18n("Please wait...")});
            this.mask.show();
        });

        // when receiving response
        this.triggerEventProtocol.events.register('crudfinished', this, this.onGotFeatures);

        // searcher
        var searcher = new mapfish.Searcher.Map({
            map: this.map,
            mode: mapfish.Searcher.Map.BOX,
            scope: this,
            searchTolerance: 10, 
            protocol: this.triggerEventProtocol
        });

        // control
        this.control = new c2corg.SearchControl({
            searcher: searcher
        });

        this.setGrids();
    },

    onGotFeatures: function(response) {
        this.clearPreviousResults();

        // TODO: handle no result case

        // FIXME no way to directly use the featureCollection? (GeoExt.data.FeatureStore?)
        var features = (new OpenLayers.Format.GeoJSON()).read(
                (new OpenLayers.Format.JSON()).write(
                    response.features
                )
            );

        // geometries are not in the good projection
        for (var i = 0, len = features.length; i < len; i++) {
            var geom = features[i].geometry;
            if (geom instanceof OpenLayers.Geometry.Point) {
                geom.transform(this.api.epsg4326, this.api.epsg900913);
            }
        }

        this.api.getDrawingLayer().addFeatures(features);
        this.summitGrid.getStore().loadData(features);
        Ext.getCmp('queryResults').expand();

        this.mask.hide();
    },

    clearPreviousResults: function() {
        Ext.getCmp('queryResults').collapse(); 
        this.api.getDrawingLayer().destroyFeatures();
        this.summitGrid.getStore().removeAll();
    },

    getComponents: function() {
        return [this.summitGrid];
    },

    setGrids: function() {

        var summitStore = new Ext.data.Store({
            reader: new GeoExt.data.FeatureReader({}, [
                {name: 'id'},
                {name: 'name'},
                {name: 'elevation'}
            ])
        });
/*      
        var summitStore = new GeoExt.data.FeatureStore({
            layer: this.api.getDrawingLayer(),
            fields: [
                {name: 'id'},
                {name: 'name'},
                {name: 'elevation'}
            ],
            autoLoad: false
        });
*/
        var summitCm = new Ext.grid.ColumnModel([{
            header: OpenLayers.i18n('id'),
            dataIndex: 'id'
        },{
            header: OpenLayers.i18n('name'),
            dataIndex: 'name'
        },{
            header: OpenLayers.i18n('elevation'),
            dataIndex: 'elevation'
        }]);

        this.summitGrid = new Ext.grid.GridPanel({
            title: OpenLayers.i18n('Summits'),
            store: summitStore,
            cm: summitCm,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        this.summitGrid.getSelectionModel().on('rowselect', this.onRowselect, this);
    },

    onRowselect: function(sm, rowIdx, r) {
        // TODO
    }
});

c2corg.SearchControl = OpenLayers.Class(OpenLayers.Control, {
    searcher: null,

    initialize: function(options) {
        OpenLayers.Control.prototype.initialize.apply(this, arguments);
    },

    activate: function() {
        if (OpenLayers.Control.prototype.activate.call(this)) {
            this.searcher.activate();
        }
    },

    deactivate: function() {
        if (OpenLayers.Control.prototype.deactivate.call(this)) {
            this.searcher.deactivate();
        }
    },

    CLASS_NAME: 'SearchControl'
});
