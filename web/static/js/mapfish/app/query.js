Ext.namespace("c2corg");

c2corg.Query = OpenLayers.Class(OpenLayers.Control.GetFeature, {
    
    api: null,
    click: false,
    box: true,
    map: null,
    control: null,
    mask: null,
    triggerEventProtocol: null,
    currentModule: null,
    currentGrid: null,

    initialize: function(options) {
        options = options || {}; 

        this.api = options.api;
        this.map = this.api.map;
        this.currentModule = 'summits';

        OpenLayers.Util.extend(options, {
            protocol: new OpenLayers.Protocol.HTTP({
                url: this.api.baseConfig.baseUrl + this.currentModule + '/geojson',
                format: new OpenLayers.Format.GeoJSON(),
                params: {}
            })
        });
        OpenLayers.Control.GetFeature.prototype.initialize.apply(this, [options]);

        this.setCurrentGrid();
        
        this.api.getDrawingLayer().events.on({
            "featureselected": this.onFeatureSelected,
            scope: this
        });
    },

    request: function(bounds, options) {

        options = options || {};

        this.clearPreviousResults();
        this.mask = new Ext.LoadMask(Ext.get('mappanel'), {msg: OpenLayers.i18n("Please wait...")});
        this.mask.show();

        if (this.map.baseLayer instanceof Geoportal.Layer.WMSC) {
            bounds = bounds.transform(this.api.fxx, this.api.epsg900913);
        }

        var filter = new OpenLayers.Filter.Spatial({
            type: this.filterType,
            value: bounds
        });

        this.protocol.read({
            maxFeatures: options.single == true ? this.maxFeatures : undefined,
            filter: filter,
            callback: function(result) {
                if(result.success()) {
                    this.select(result.features);
                }
                // Reset the cursor.
                OpenLayers.Element.removeClass(this.map.viewPortDiv, "olCursorWait");
            },
            scope: this
        });

    },

    updateQueryUrl: function() {
        var queryUrl = this.api.baseConfig.baseUrl + this.currentModule + '/geojson';
        // for some reason, queryUrl must be updated in both following places
        this.protocol.url = queryUrl;
        this.protocol.options.url = queryUrl;
    },

    select: function(features) {

        if (features.length > 0) {
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
        }

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

                    // remove existing markers
                    this.api.getDrawingLayer().destroyFeatures();
                    
                    // update query service URL
                    this.updateQueryUrl();

                    // update results grid
                    var resultsPanel = Ext.getCmp('queryResults');
                    resultsPanel.collapse();
                    
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
    },

    activate: function() {
        // deactivate tooltip controls when activating the query tool to avoid conflicts
        this.api.tooltip.deactivate();
        this.api.tooltipTest.deactivate();

        return OpenLayers.Control.GetFeature.prototype.activate.call(this);
    },

    deactivate: function() {
        // reactivate tooltip when query tool is turned off
        this.api.tooltip.activate();
        this.api.tooltipTest.activate();

        return OpenLayers.Control.GetFeature.prototype.deactivate.call(this);
    }
});
