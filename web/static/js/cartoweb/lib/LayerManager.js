CartoWeb.LayerManager = OpenLayers.Class.create();
CartoWeb.LayerManager.prototype = {

    PARAMS: {
        service: "WMS",
        version: "1.1.1",
        request: "GetLegendGraphic",
        exceptions: "application/vnd.ogc.se_inimage",
        format: "image/png"
    },

    olLayer: null,

    initialize: function(olLayer, layers, id, wms_url) {
        this.olLayer = olLayer;
  
        var form = $('CWLayerManagerForm');
        if (!form) {
             var form = document.createElement("form");
             form.id = 'CWLayerManagerForm';
             $(id).appendChild(form);
        } 

        for (var layer_id in layers) {
            var layer_info = layers[layer_id];
            
            var div = document.createElement("div");
            
            var checkbox = document.createElement("input");
            checkbox.id = layer_id;
            checkbox.type = "checkbox";
            
            div.appendChild(checkbox);
            OpenLayers.Event.observe(checkbox, 'click', this.updateLayer.bindAsEventListener(this), false);       
            
            var label = document.createElement("label");
            label.setAttribute("from", layer_id);
                                 
            var params = OpenLayers.Util.extend({LAYER: layer_id}, this.PARAMS);
            var params_string = OpenLayers.Util.getParameterString(params);
            var img = document.createElement("img");
            img.setAttribute("src", wms_url + params_string);
            label.appendChild(img);

            var text = document.createTextNode(layer_info['name']);
            label.appendChild(text);

            div.appendChild(label);
            
            form.appendChild(div);
            
            // IE doesn't actually check the checkbox if it's marked
            // as checked before the div is appended.
            checkbox.checked = layer_info['visible'] ? true : false;
        }
    },
    
    updateLayer: function(evt) {
        var sublayer = OpenLayers.Event.element(evt).id;
        var layers = this.olLayer.params.LAYERS;
        
        if (layers.indexOf(sublayer) < 0) {
            layers.push(sublayer);
        } else {
            OpenLayers.Util.removeItem(layers, sublayer);
        }
        
        if (layers.length <= 0) {
            // no sublayers selected, make the layer invisible
            this.olLayer.setVisibility(false);
        	return;
        }
        
        // make sure the layer is visible before anything
        this.olLayer.setVisibility(true);     
        this.olLayer.mergeNewParams(layers);
    }
}
