Ext.namespace("c2corg");

c2corg.Query = OpenLayers.Class({

    api: null,
    map: null,
    control: null,
    summitGrid: null,
    mask: null,
    triggerEventProtocol: null,
    currentModule: null,

    initialize: function(options) {

        this.api = options.api;
        this.map = this.api.map;

        this.currentModule = 'summits';

        var protocol = new mapfish.Protocol.MapFish({
            url: this.api.baseConfig.baseUrl + this.currentModule + '/geojson',
            format: new OpenLayers.Format.GeoJSON()
        });

        // triggerEventProtocol used to be able to add the layers list into the parameter on clic and handle response
        this.triggerEventProtocol = new mapfish.Protocol.TriggerEventDecorator({
            protocol: protocol
        });

        // before sending query
        this.triggerEventProtocol.events.register('crudtriggered', this, function(obj) {
            this.clearPreviousResults();
            this.mask = new Ext.LoadMask(Ext.get('mappanel'), {msg: OpenLayers.i18n("Please wait...")});
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
            projection: this.api.epsg900913,
            protocol: this.triggerEventProtocol
        });

        // control
        this.control = new c2corg.SearchControl({
            searcher: searcher
        });

        this.setGrids();
    },

    setQueryUrl: function(module) {
        this.currentModule = module || 'summits';
        this.triggerEventProtocol.protocol.url = this.api.baseConfig.baseUrl + this.currentModule + '/geojson';
    },

    onGotFeatures: function(response) {
        var features = response.features;
        if (!features || features.length == 0) {
            this.mask.hide();
            // TODO: better solution to warn there is no result?
            this.api.showPopup({
                title: OpenLayers.i18n('search results'),
                html: OpenLayers.i18n('no item found')
            });
            return;
        }

        // geometries are not in the good projection
        var projection = this.map.baseLayer.CLASS_NAME == "Geoportal.Layer.WMSC" ?
                         this.api.fxx : this.api.epsg900913;
        for (var i = 0, len = features.length; i < len; i++) {
            var geom = features[i].geometry;
            if (geom instanceof OpenLayers.Geometry.Point) {
                geom.transform(this.api.epsg4326, projection);
            }
        }

        this.api.getDrawingLayer().addFeatures(features);
        this.summitGrid.getStore().loadData(features);
        Ext.getCmp('queryResults').expand();

        this.mask.hide();
        
        // recenter on features
        this.api.map.zoomToExtent(this.api.getDrawingLayer().getDataExtent());
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
                {name: 'module'},
                {name: 'elevation'}
            ])
        });

        var summitCm = new Ext.grid.ColumnModel([{
            header: OpenLayers.i18n('name'),
            dataIndex: 'name',
            width: 300,
            renderer: this.linkify
        },{
            header: OpenLayers.i18n('elevation'),
            dataIndex: 'elevation',
            width: 60,
            renderer: function(value) { return value + ' m'; }
        }]);

        this.summitGrid = new Ext.grid.GridPanel({
            title: OpenLayers.i18n('Summits'),
            store: summitStore,
            cm: summitCm,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        this.summitGrid.getSelectionModel().on('rowselect', this.onRowselect, this);

        this.api.getDrawingLayer().events.on({
            "featureselected": this.onFeatureSelected,
            scope: this
        });
    },

    onRowselect: function(sm, rowIdx, r) {
        if (this.api.selectCtrl) {
            this.api.selectCtrl.unselectAll();
            this.api.selectCtrl.select(this.api.getDrawingLayer().getFeatureById(r.id));
        }
    },

    onFeatureSelected: function(f) {
        var grid = this.summitGrid; // FIXME: depends on current selected module
        var rows = grid.getView().getRows();
        for (var i = 0; i < rows.length; i++) {
            var row = grid.getView().findRowIndex(rows[i]);
            var record = grid.store.getAt(row);
            if (record.data.fid == f.feature.fid) {
                grid.getView().addRowClass(row, "x-grid3-row-over");
            } else {
                grid.getView().removeRowClass(row, "x-grid3-row-over");    
            }
        }
    },

    linkify: function(value, metadata, record) {
        var url = '/' + record.data.module + '/' + record.data.id;
        return '<a href="' + url + '">' + value + '</a>';
    },

    // TODO
    getFormPanel: function() {
        /*
        // FIXME: adapt form to selected module
        return new Ext.FormPanel({
            border: false,
            defaults: {
                hideLabel: true,
                anchor: '100%',
                border: false
            },
            items: [{
                layout: 'table',
                cls: 'mapSearchField',
                defaults: {
                    border: false
                },
                items: [{
                    html: OpenLayers.i18n('elevation')
                },{
                    xtype: 'combo',
                    width: 20,
                    store: ['>', '<'],
                    value: '>',
                    triggerAction: 'all',
                    editable: false,
                    mode: 'local',
                    forceSelection: true,
                    name: 'elevation_op'
                },{
                    xtype: 'textfield',
                    name: 'elevation',
                    width: 50
                }]
            }]
        });
        */
    },
    
    getQueryTypesStore: function() {
        var queryableLayers = ['summits', 'parkings', 'huts', 'sites', 'users', 'images', 'routes', 'outings', 'maps', 'areas'];
        var layer, layersData = []; 
        for (var i = 0, len = queryableLayers.length; i < len; i++) {
            layer = queryableLayers[i];
            layersData.push([layer, OpenLayers.i18n(layer)]);
        }
        return new Ext.data.SimpleStore({
            fields: ['id', 'name'],
            data: layersData
        });
    },
    
    getQueryCombo: function() {
        return {
            xtype: 'combo',
            width: 150,
            hideLabel: true,
            mode: 'local',
            store: this.getQueryTypesStore(),
            displayField: 'name',
            valueField: 'id',
            value: 'summits',
            forceSelection: true,
            editable: false,
            triggerAction: 'all',
            listeners: {
                select: function(combo, record, index) {
                    var layer = record.data.id;
                    this.setQueryUrl(layer);
                    
                    // make sure matching layer is displayed
                    this.api.tree.setNodeChecked(layer, true);
                },
                scope: this
            }
        }
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
