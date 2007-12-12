CartoWeb.SearchForm = OpenLayers.Class.create();
CartoWeb.SearchForm.prototype =
    OpenLayers.Class.inherit(CartoWeb.Search, {

    form: null,

    initialize: function(formId, url, callback, search) {
        CartoWeb.Search.prototype.initialize.call(this, url, callback, search);
        this.form = $(formId);
    },

    getParams: function() {   
        var params = new Object();
        var form = this.form;

        /* process <input> elements */
        var inputElements = form.getElementsByTagName('input');
        for (var i = 0; i < inputElements.length; i++) {
            // Collect the current input only if it's is not 'submit', 'image' or 'button'
            currentElement = inputElements.item(i);

            if (currentElement.disabled == true) {
                continue;
            }
            inputType = currentElement.getAttribute('type');

            if (inputType == 'radio' || inputType == 'checkbox') {
                if (currentElement.checked) {
                    params = OpenLayers.Util.extend(params, this.getParamsFromInput(currentElement));
                }
            } else if (inputType == 'submit' || inputType == 'button' || inputType == 'image') {
                // Do nothing. Sending the submit inputs in POST Request would make
                // the serverside act like all buttons on the form were clicked.
                // And we don't want that.
            } else {
                params = OpenLayers.Util.extend(params, this.getParamsFromInput(currentElement));
            }
        }

        /* process <select> elements */
        var selectElements = form.getElementsByTagName('select');
        for (var i = 0; i < selectElements.length; i++) {
            // Get the param name (i.e. fetch the name attr)
            var currentElement = selectElements.item(i);
            var paramName = currentElement.getAttribute('name');
            var multiple = false;
            if (paramName.indexOf('[]') == paramName.length - 2) {
                // <select> multiple
                multiple = true;
                paramName = paramName.replace(/\[\]/, '');
            }
            // Get the param value(s)
            // (i.e. fetch the checked options element's value attr)
            var optionElements = currentElement.getElementsByTagName('option');
            for (var j = 0; j < optionElements.length; j++) {
                currentElement = optionElements.item(j);
                if (currentElement.selected) {
                    paramValue = currentElement.getAttribute('value');
                    if (paramValue == null) {
                        paramValue = '';
                    }
                    param = {};
                    if (multiple) {
                        param[paramName + '[' + j + ']'] = paramValue;
                    } else {
                        param[paramName] = paramValue;
                    }
                    params = OpenLayers.Util.extend(params, param);
                }
            }
        }
        return params;
    },

    /**
     * General method that builds a request string from an HTMLFormElement and
     * returns a formatted string: 'elemeentName=elemeentValue' or 'elemeentName='
     * @param HTMLFormElement
     */
    getParamsFromInput: function(htmlElement) {

        var inputType = htmlElement.getAttribute('type');
        var paramName = htmlElement.getAttribute('name');

        if (inputType == 'text') {
            paramValue = htmlElement.value;
        } else {
            paramValue = htmlElement.getAttribute('value');
        }

        var ret = new Object();

        if (paramValue != null) {
             ret[paramName] = paramValue;
        } else {
            // HTTP POST requests parameters HAVE TO be followed by '='
            // even when they have no associated value
            ret[paramName] = null;
        }
         
        return ret;
    }
});
