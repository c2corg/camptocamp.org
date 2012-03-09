/*
 * This is a small adaptation of existing GeoNamesSearchCombo from mapfish
 * since we wanted to adapt some parameters that cannot be overriden
 *
 * requirements (MapFishApi/js/mapfish_api.js, OpenLayers/BaseTypes/LonLat.js, OpenLayers/Projection.js)
 * are put in include.js
 */

Ext.namespace("c2corg");

c2corg.GeoNamesSearchCombo = Ext.extend(Ext.form.ComboBox, {
    /**
     * Property: api
     * {MapFish.API} instance
     */
    api: null,

    minChars: 2,

    queryDelay: 50,

    id: 'GeonamesCombo',

    hideTrigger: true,

    displayField: 'name',

    forceSelection: true,

    queryParam: 'name_startsWith',

    tpl: '<tpl for="."><div class="x-combo-list-item"><h1>{name}<br></h1>{adminName1} - {countryName}</div></tpl>',


    initComponent: function() {
        c2corg.GeoNamesSearchCombo.superclass.initComponent.call(this);

        this.store = new Ext.data.Store({
            proxy: new Ext.data.ScriptTagProxy({
                // note: normally, we should put featureClass in baseParams, but we have to define it two times
                // in order to get places and summits
                url: 'http://ws.geonames.org/searchJSON?featureClass=P&featureClass=T',
                method: 'GET'
            }),
            baseParams: {
                maxRows: '10',
                lang: document.documentElement.lang
            },
            reader: new Ext.data.JsonReader({
                idProperty: 'geonameId',
                root: "geonames",
                totalProperty: "totalResultsCount",
                fields: [
                    {
                        name: 'geonameId'
                    },
                    {
                        name: 'countryName'
                    },
                    {
                        name: 'lng'
                    },
                    {
                        name: 'lat'
                    },
                    {
                        name: 'name'
                    },
                    {
                        name: 'adminName1'
                    }
                ]  })
        });
        this.store.load();

        this.on("select", function(combo, record, index) {
            var position = new OpenLayers.LonLat(record.data.lng, record.data.lat);
            position.transform(new OpenLayers.Projection("EPSG:4326"), this.api.map.getProjectionObject());
            this.api.map.setCenter(position, 14);
        }, this);


        //fields: ['countryName', 'adminCode1', 'fclName', 'countryCode', 'lng', 'fcodeName', 'fcl','name','fcode', 'geonameId', 'lat', 'population', 'adminName1' ]
    },

    initList : function() {
        c2corg.GeoNamesSearchCombo.superclass.initList.call(this);
        // record the click target dom node.
        this.view.on('beforeclick', function(view, index, node, event) {
            this.lastTarget = event.getTarget();
        }, this);
    }
});
