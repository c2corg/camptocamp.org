/**
 * Builds document-embedded map using c2corg maps API.
 */

Ext.namespace("c2corg");

c2corg.embeddedMap = (function() {
    
    var api = new c2corg.API({
        //lang: lang // TODO: get lang from a JS var set in php template 
    });
    
    objectCoords = objectCoords || {};
    
    api.createMap({
        easting: objectCoords.lon,
        northing: objectCoords.lat,
        zoom: objectCoords.zoom
    });
    // TODO: hilight object
    
    var initialCenter = api.map.getCenter();
    var initialZoom = api.map.getZoom();
    
    var bbar = api.createBbar();
    
    var addPermalinkButton = function() {
        var permalinkDiv = document.createElement("div");
        permalinkDiv.id = 'permalink' + api.apiId;
        Ext.getBody().appendChild(permalinkDiv);
        api.addPermalinkControl();
        bbar.add(new Ext.Action({
            text: OpenLayers.i18n('Permalink.openlink'),
            handler: function() {
                window.open(Ext.get('permalink' + api.apiId).dom.value);
            }
        }));
    };
    
    var addResetButton = function() {
        bbar.add(new Ext.Action({
            text: OpenLayers.i18n('Reset'),
            handler: function() {
                var center = initialCenter.clone();
                api.map.setCenter(center.transform(api.epsg900913, api.map.getProjectionObject()),
                                  initialZoom); // FIXME: zoom changes depending on base layer resolutions nb
            }
        }));
    }
    
    var mappanel = Ext.apply(api.createMapPanel(), {
        id: 'mappanel',
        margins: '0 0 0 0',
        region: 'center',
        border: false,
        bbar: bbar
    });
    
    var layertree = Ext.apply(api.createLayerTree(), {
        region: 'west',
        width: 200,
        border: false,
        collapsible: true,
        collapseMode: 'mini',
        split: true
    });
    
    return {
        init: function() {
            // hide loading message
            Ext.removeNode(Ext.getDom('mapLoading'));
            
            new Ext.Panel({
                applyTo: 'map',
                layout: 'border',
                border: false,
                cls: 'embeddedMap',
                items: [ mappanel, layertree ]
            });
            
            addResetButton();
            addPermalinkButton();
        }
    }
})();

Ext.onReady(c2corg.embeddedMap.init);