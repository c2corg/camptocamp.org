/*
 * Copyright 2008 Institut Geographique National France, released under the
 * BSD license.
 */
/*
 * @requires Geoportal/Control.js
 */
/**
 * Class: Geoportal.Control.Logo
 * The Geoportal framework distributor class.
 * Allow to add logo on the map
 *
 * Inherits from:
 * - {<Geoportal.Control>}
 */
Geoportal.Control.Logo=
    OpenLayers.Class( Geoportal.Control, {

    /**
     * APIProperty: logoPrefix
     * {String} prefix used when the logo is a key. Usually, it is the path to
     * the image, the key being the image basename without the extension.
     *      Default *'http://www.geoportail.fr/legendes/logo_'*
     */
    logoPrefix: 'http://www.geoportail.fr/legendes/logo_',

    /**
     * APIProperty: logoSuffix
     * {String} suffix to be applied to the logo. Usually, it is the image's
     * extension (e.g., '.png').
     *      Default *'.gif'*
     */
    logoSuffix: '.gif',

    /**
     * APIProperty: logoSize
     * {<OpenLayers.Size>} Size in width and height of logo image.
     *      Default *Geoportal.Control.Logo.WHSizes.normal*,
     *      *Geoportal.Control.Logo.WHSizes.normal*
     */
    logoSize: null,

    /**
     * Property: _listeLogos
     * {Array({DOMElement})} List of logo (displayed or not)
     */
    _listeLogos: null,

    /**
     * Constructor: Geoportal.Control.Logo
     * Build the control
     *
     * Parameters:
     * options - {DOMElement} Options for control.
     *           logoSize option indicates the width and height of logos.
     */
    initialize: function(options) {
        Geoportal.Control.prototype.initialize.apply(this, arguments);
        if (!this.logoSize) {
            this.logoSize= new OpenLayers.Size(
                Geoportal.Control.Logo.WHSizes.normal,Geoportal.Control.Logo.WHSizes.normal);
        }
        if (typeof(this.logoSize)=='number') {
            this.logoSize= new OpenLayers.Size(this.logoSize,this.logoSize);
        }
        this._listeLogos= [];
    },

    /**
     * APIMethod: destroy
     * Unregister events and delete control
     */
    destroy: function() {
        var childs= this.div.childNodes;
        for (var i= 0; i < childs.length; i++) {
            this.div.removeChild(childs[i]);
        }
        this._listeLogos= null;

        this.map.events.unregister("addlayer", this, this.redraw);
        this.map.events.unregister("changelayer", this, this.redraw);
        this.map.events.unregister("removelayer", this, this.redraw);
        this.map.events.unregister("zoomend", this, this.redraw);

        Geoportal.Control.prototype.destroy.apply(this, arguments);
    },

    /**
     * APIMethod: draw
     * Call the default draw, and then draw the control.
     *
     * Parameters:
     * px - {<OpenLayers.Pixel>} the position where to draw the control.
     *
     * Returns:
     * {DOMElement} the control's div.
     */
    draw: function(px) {
        Geoportal.Control.prototype.draw.apply(this, arguments);
        return this.div;
    },

    /**
     * APIMethod: redraw
     * Display the logo of the distributors of the current active layers.
     */
    redraw: function() {
        // on masque tous les logos actifs
        var i;
        var childs= this.div.childNodes;
        for (i= 0; i < childs.length; i++) {
            childs[i].style.display= "none";
        }

        var layers= this.map.layers;
        var zoom= this.map.getZoom();
        var logo;
        for (i= 0; i < layers.length; i++) {
            //if (!layers[i].getVisibility() || !layers[i].inRange) { // FIXME: inRange is always false for gp layers
            if (!layers[i].getVisibility()) {
                continue;
            }

            if (layers[i].originators!=null) {
                var afficherLogo;
                for (var j= 0; j < layers[i].originators.length; j++) {
                    afficherLogo= true;
                    var logo= layers[i].originators[j];
                    if (((logo.minZoomLevel) && (logo.minZoomLevel > zoom)) ||
                        ((this.map.minZoomLevel) && this.map.minZoomLevel > zoom)) {
                        afficherLogo= false;
                    }
                    if (((logo.maxZoomLevel) && (logo.maxZoomLevel < zoom)) ||
                        ((this.map.maxZoomLevel) && this.map.maxZoomLevel < zoom)) {
                        afficherLogo= false;
                    }
                    if (afficherLogo) {
                        this._ajoutLogo(logo.logo, logo.url, logo.pictureUrl);
                    }
                }
            }
        }
    },

    /**
     * Method: _ajoutLogo
     * Add a logo to the control
     *
     * Parameters:
     * logo - {String} logo acronym.
     *      the final url is the concatenation of logoPrefix, logo parameter
     *      and logoSuffix.
     * url - {String} url associated with the logo.
     * pictureUrl - {String} logo url. Takes precedence over logo parameter.
     */
    _ajoutLogo: function(logo, url, pictureUrl) {
        if (this._listeLogos[logo]==null) {
            var divLogo= document.createElement("div");
            this.div.appendChild(divLogo);
            this._listeLogos[logo]= divLogo;

            var imgLogo= OpenLayers.Util.createImage(null,null,
                                                     new OpenLayers.Size(this.logoSize.w,this.logoSize.h),
                                                     pictureUrl?
                                                        pictureUrl :
                                                        this.logoPrefix + logo + this.logoSuffix,
                                                     null,null,null,false);

            if (url!=null) {
                var aLogo= document.createElement("a");
                aLogo.setAttribute("href", url);
                aLogo.setAttribute("target", "_blank");
                aLogo.appendChild(imgLogo);
                divLogo.appendChild(aLogo);
            } else {
                divLogo.appendChild(imgLogo);
            }
        } else {
            this._listeLogos[logo].style.display= "";
        }
    },

    /**
     * APIMethod: setMap
     * Set map and register events
     *
     * Parameters:
     * map - {OpenLayers.Map}
     */
    setMap: function() {
        Geoportal.Control.prototype.setMap.apply(this, arguments);

        this.map.events.register("addlayer", this, this.redraw);
        this.map.events.register("changelayer", this, this.redraw);
        this.map.events.register("removelayer", this, this.redraw);
        this.map.events.register("zoomend", this, this.redraw);
    },

    /**
     * APIMethod: changeLogoSize
     * Changes the size of displayed logos.
     *
     * Parameters:
     * size - {Integer | <OpenLayers.Size>} new size in pixels
     */
    changeLogoSize: function(size) {
        var oSize= null;
        if (typeof(size)=='number') {
            oSize= new OpenLayers.Size(size,size);
        } else {
            oSize= size.clone();
        }
        this.logoSize= oSize;
        var layers= this.map.layers;
        for (var i= 0, il= layers.length; i < il; i++) {
            var layer= layers[i];
            if (layer.originators!=null) {
                for (var j= 0, jl= layer.originators.length; j < jl; j++) {
                    var logo= layer.originators[j];
                    if (this._listeLogos[logo.logo]!=null) {
                        var img= this._listeLogos[logo.logo].firstChild;
                        if (img) {
                            if (img.firstChild) { // it is the anchor, find the img !
                                img= img.firstChild;
                            }
                            if (img) {
                                img.style.width= this.logoSize.w+"px";
                                img.style.height= this.logoSize.h+"px";
                            }
                        }
                    }
                }
            }
        }
        oSize= null;
    },

    /**
     * Constant: CLASS_NAME
     * {String} *"Geoportal.Control.Logo"*
     */
    CLASS_NAME: "Geoportal.Control.Logo"
});

/**
 * Constant: Geoportal.Control.Logo.WHSizes
 * {Object} Square sizes of logo in pixels.
 *      They depend on the mode :
 *      - normal : *50*
 *      - mini   : *30*
 */
Geoportal.Control.Logo.WHSizes= {
    'normal':50,
    'mini'  :30
};
