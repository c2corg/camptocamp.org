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
                this.mask.hide();
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

        this.clearPreviousResults();

        if (features && features.length > 0) {
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
        Ext.getCmp('clearFeaturesButton').enable();

        // recenter on features
        this.api.map.setCenter(this.api.getDrawingLayer().getDataExtent().getCenterLonLat());
    },

    clearPreviousResults: function() {
        this.api.getDrawingLayer().destroyFeatures();
        this.currentGrid.getStore().removeAll();
    },

    getGrid: function(module) {
       module = module || this.currentModule;
       var documents = new c2corg.Document({id: module, api: this.api});
       return documents.grid;
    },

    setCurrentGrid: function() {
        this.currentGrid = this.getGrid();
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
        var queryableLayers = ['summits', 'parkings', 'huts', 'sites', 'users', 'images', 'routes'/*, 'outings', 'maps', 'areas'*/];
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
        this.api.deactivateTooltip();
        return OpenLayers.Control.GetFeature.prototype.activate.call(this);
    },

    deactivate: function() {
        // reactivate tooltip when query tool is turned off
        this.api.activateTooltip();
        return OpenLayers.Control.GetFeature.prototype.deactivate.call(this);
    }
});

c2corg.Document = OpenLayers.Class({

    grid: null,
    id: null,
    api: null,

    initialize: function(options) {
        this.api = options.api;
        this.id = options.id;

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

        this.grid = new Ext.grid.GridPanel({
            id: this.id,
            title: OpenLayers.i18n(options.title || this.id),
            store: store,
            cm: cm,
            sm: new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        this.grid.getSelectionModel().on('rowselect', this.onRowselect, this);
    },

    onRowselect: function(sm, rowIdx, r) {
        // recenter on item
        //var geom = r.data.feature.geometry;
        //this.api.map.setCenter(new OpenLayers.LonLat(geom.x, geom.y));

        // hilight marker
        if (this.api.selectCtrl) {
            this.api.selectCtrl.unselectAll();
            this.api.selectCtrl.select(this.api.getDrawingLayer().getFeatureById(r.id));
        }
    },

    linkify: function(value, metadata, record) {
        var url = '/' + record.data.module + '/' + record.data.id;
        return '<a href="' + url + '">' + value + '</a>';
    }
});
