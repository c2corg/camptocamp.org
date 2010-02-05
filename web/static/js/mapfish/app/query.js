Ext.namespace("c2corg");

c2corg.Query = OpenLayers.Class({

    api: null,
    map: null,
    control: null,
    mask: null,
    triggerEventProtocol: null,
    currentModule: null,
    currentGrid: null,

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
            searcher: searcher,
            api: api
        });

        this.setCurrentGrid();
        
        this.api.getDrawingLayer().events.on({
            "featureselected": this.onFeatureSelected,
            scope: this
        });
    },

    updateQueryUrl: function() {
        var queryUrl = this.api.baseConfig.baseUrl + this.currentModule + '/geojson';
        // for some reason, queryUrl must be updated in both following places
        this.triggerEventProtocol.options.protocol.url = queryUrl;
        this.triggerEventProtocol.protocol.options.url = queryUrl;
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
        var projection = this.map.baseLayer instanceof Geoportal.Layer.WMSC ?
                         this.api.fxx : this.api.epsg900913;
        for (var i = 0, len = features.length; i < len; i++) {
            var geom = features[i].geometry;
            if (geom instanceof OpenLayers.Geometry.Point) {
                geom.transform(this.api.epsg4326, projection);
            }
        }

        this.api.getDrawingLayer().addFeatures(features);
        this.currentGrid.getStore().loadData(features);
        Ext.getCmp('queryResults').expand();

        this.mask.hide();
        
        // recenter on features
        //this.api.map.zoomToExtent(this.api.getDrawingLayer().getDataExtent());
    },

    clearPreviousResults: function() {
        this.api.getDrawingLayer().destroyFeatures();
        this.currentGrid.getStore().removeAll();
    },

    getGrid: function(module) {
       module = module || this.currentModule;
       switch (module) {
           case 'summits': return this.getSummitsGrid();
           case 'parkings': return this.getParkingsGrid();
           // TODO: default?
       }
    },

    setCurrentGrid: function() {
        this.currentGrid = this.getGrid();
    },

    getSummitsGrid: function() {
        var store = new Ext.data.Store({
            reader: new GeoExt.data.FeatureReader({}, [
                {name: 'id'},
                {name: 'name'},
                {name: 'module'},
                {name: 'elevation'}
            ])
        });

        var cm = new Ext.grid.ColumnModel([{
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

        var grid = new Ext.grid.GridPanel({
            id: 'summits',
            title: OpenLayers.i18n('summits'),
            store: store,
            cm: cm,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        grid.getSelectionModel().on('rowselect', this.onRowselect, this);

        return grid;
    },

    getParkingsGrid: function() {
        var store = new Ext.data.Store({
            reader: new GeoExt.data.FeatureReader({}, [
                {name: 'id'},
                {name: 'name'},
                {name: 'module'},
                {name: 'elevation'}
            ])
        });

        var cm = new Ext.grid.ColumnModel([{
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

        var grid = new Ext.grid.GridPanel({
            id: 'parkings',
            title: OpenLayers.i18n('parkings'),
            store: store,
            cm: cm,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        grid.getSelectionModel().on('rowselect', this.onRowselect, this);
    
        return grid;
    },

    onRowselect: function(sm, rowIdx, r) {
        if (this.api.selectCtrl) {
            this.api.selectCtrl.unselectAll();
            this.api.selectCtrl.select(this.api.getDrawingLayer().getFeatureById(r.id));
        }
    },

    onFeatureSelected: function(f) {
        var grid = this.currentGrid;
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
        var queryableLayers = ['summits', 'parkings'];//, 'huts', 'sites', 'users', 'images', 'routes', 'outings', 'maps', 'areas'];
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
                    var module = record.data.id;

                    if (module == this.currentModule) return; // nothing to change

                    this.currentModule = module;
                    
                    // update query service URL
                    this.updateQueryUrl();

                    // update results grid
                    var resultsPanel = Ext.getCmp('queryResults');
                    
                    // remove previous grid, now useless
                    resultsPanel.remove(this.currentGrid);
                    
                    // update current grid and add it to panel
                    this.setCurrentGrid();
                    resultsPanel.add(this.currentGrid);

                    // refresh panel
                    resultsPanel.doLayout();
                    
                    // make sure matching layer is displayed
                    this.api.tree.setNodeChecked(module, true);
                },
                scope: this
            }
        }
    }
});

c2corg.SearchControl = OpenLayers.Class(OpenLayers.Control, {
    
    searcher: null,
    api: null,

    initialize: function(options) {
        this.api = options.api;
        OpenLayers.Control.prototype.initialize.apply(this, arguments);
    },

    activate: function() {
        if (OpenLayers.Control.prototype.activate.call(this)) {
            this.searcher.activate();
        }

        // deactivate tooltip controls when activating the query tool to avoid conflicts
        this.api.tooltip.deactivate();
        this.api.tooltipTest.deactivate();
    },

    deactivate: function() {
        if (OpenLayers.Control.prototype.deactivate.call(this)) {
            this.searcher.deactivate();
        }

        // reactivate tooltip when query tool is turned off
        this.api.tooltip.activate();
        this.api.tooltipTest.activate();
    },

    CLASS_NAME: 'SearchControl'
});
