CartoWeb.Search = OpenLayers.Class.create();
CartoWeb.Search.prototype = {
    
    url: null,
    
    preCallback: null,
    
    postCallback: null,

    searchObject: null,
    
    request: null,

    initialize: function(url, preCallback, postCallback, searchObject) {
        this.url = url;
        this.preCallback = preCallback;
        this.postCallback = postCallback;
        this.searchObject = searchObject;
    },
    
    /**
     * To be overriden by subclasses.
     */
    getParams: function() {
        return {};
    },
    
    search: function() {
        // build params string
        var params = OpenLayers.Util.extend(new Object(), this.getParams());
        if (this.searchObject) {
            OpenLayers.Util.extend(params, this.searchObject.getParams());
        }
        var paramsString = OpenLayers.Util.getParameterString(params);
        
        // build full request string
        var url = this.url;
        var requestString = url;
        if (paramsString != "") {
            var lastServerChar = url.charAt(url.length - 1);
            if ((lastServerChar == "&") || (lastServerChar == "?")) {
                requestString += paramsString;
            } else {
                if (url.indexOf('?') == -1) {
                    // requestString has no '?', add one
                    requestString += '?' + paramsString;
                } else {
                    // requestString contains '?', so must already have params
                    requestString += '&' + paramsString;
                }
            }
        }

        if (this.request) {
            this.request.transport.abort();
        }
        // send request
        if (this.preCallback) {
            this.preCallback();
        }
        this.request = new OpenLayers.Ajax.Request(requestString, {onComplete: this.postCallback});
    }
}
