/* ======================================================================
    OpenLayers/Lang/en.js
   ====================================================================== */

/**
 * @requires OpenLayers/Lang.js
 */

/**
 * Namespace: OpenLayers.Lang["en"]
 * Dictionary for English.  Keys for entries are used in calls to
 *     <OpenLayers.Lang.translate>.  Entry bodies are normal strings or
 *     strings formatted for use with <OpenLayers.String.format> calls.
 */
OpenLayers.Lang.en = {

    'unhandledRequest': "Unhandled request return ${statusText}",

    'Permalink': "Permalink",

    'Overlays': "Overlays",

    'Base Layer': "Base Layer",

    'noFID': "Can't update a feature for which there is no FID.",

    'browserNotSupported':
        "Your browser does not support vector rendering. Currently supported renderers are:\n${renderers}",

    // console message
    'minZoomLevelError':
        "The minZoomLevel property is only intended for use " +
        "with the FixedZoomLevels-descendent layers. That this " +
        "wfs layer checks for minZoomLevel is a relic of the" +
        "past. We cannot, however, remove it without possibly " +
        "breaking OL based applications that may depend on it." +
        " Therefore we are deprecating it -- the minZoomLevel " +
        "check below will be removed at 3.0. Please instead " +
        "use min/max resolution setting as described here: " +
        "http://trac.openlayers.org/wiki/SettingZoomLevels",

    'commitSuccess': "WFS Transaction: SUCCESS ${response}",

    'commitFailed': "WFS Transaction: FAILED ${response}",

    'googleWarning':
        "The Google Layer was unable to load correctly.<br><br>" +
        "To get rid of this message, select a new BaseLayer " +
        "in the layer switcher in the upper-right corner.<br><br>" +
        "Most likely, this is because the Google Maps library " +
        "script was either not included, or does not contain the " +
        "correct API key for your site.<br><br>" +
        "Developers: For help getting this working correctly, " +
        "<a href='http://trac.openlayers.org/wiki/Google' " +
        "target='_blank'>click here</a>",

    'getLayerWarning':
        "The ${layerType} Layer was unable to load correctly.<br><br>" +
        "To get rid of this message, select a new BaseLayer " +
        "in the layer switcher in the upper-right corner.<br><br>" +
        "Most likely, this is because the ${layerLib} library " +
        "script was not correctly included.<br><br>" +
        "Developers: For help getting this working correctly, " +
        "<a href='http://trac.openlayers.org/wiki/${layerLib}' " +
        "target='_blank'>click here</a>",

    'Scale = 1 : ${scaleDenom}': "Scale = 1 : ${scaleDenom}",
    
    //labels for the graticule control
    'W': 'W',
    'E': 'E',
    'N': 'N',
    'S': 'S',
    'Graticule': 'Graticule',

    // console message
    'reprojectDeprecated':
        "You are using the 'reproject' option " +
        "on the ${layerName} layer. This option is deprecated: " +
        "its use was designed to support displaying data over commercial " + 
        "basemaps, but that functionality should now be achieved by using " +
        "Spherical Mercator support. More information is available from " +
        "http://trac.openlayers.org/wiki/SphericalMercator.",

    // console message
    'methodDeprecated':
        "This method has been deprecated and will be removed in 3.0. " +
        "Please use ${newMethod} instead.",

    // **** end ****
    'end': ''
    
};
/* ======================================================================
    GeoExt/Lang.js
   ====================================================================== */

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt
 *  class = Lang
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("GeoExt");

/** api: constructor
 *  .. class:: Lang
 *
 *      The GeoExt.Lang singleton is created when the library is loaded.
 *      Include all relevant language files after this file in your build.
 */
GeoExt.Lang = new (Ext.extend(Ext.util.Observable, {

    /** api: property[locale]
     *  ``String``
     *  The current language tag.  Use :meth:`set` to set the locale.  Defaults
     *  to the browser language where available.
     */
    locale: navigator.language || navigator.userLanguage,

    /** private: property[dict]
     *  ``Object``
     *  Dictionary of string lookups per language.
     */
    dict: null,

    /** private: method[constructor]
     *  Construct the Lang singleton.
     */
    constructor: function() {
        this.addEvents(
            /** api: event[localize]
             *  Fires when localized strings are set.  Listeners will receive a
             *  single ``locale`` event with the language tag.
             */
            "localize"
        );
        this.dict = {};
        Ext.util.Observable.constructor.apply(this, arguments);
    },

    /** api: method[add]
     *  :param locale: ``String`` A language tag that follows the "en-CA"
     *      convention (http://www.ietf.org/rfc/rfc3066.txt).
     *  :param lookup: ``Object`` An object with properties that are dot
     *      delimited names of objects with localizable strings (e.g.
     *      "GeoExt.VectorLegend.prototype").  The values for these properties
     *      are objects that will be used to extend the target objects with
     *      localized strings (e.g. {untitledPrefix: "Untitiled "})
     *
     *  Add translation strings to the dictionary.  This method can be called
     *  multiple times with the same language tag (locale argument) to extend
     *  a single dictionary.
     */
    add: function(locale, lookup) {
        var obj = this.dict[locale];
        if (!obj) {
            this.dict[locale] = Ext.apply({}, lookup);
        } else {
            for (var key in lookup) {
                obj[key] = Ext.apply(obj[key] || {}, lookup[key]);
            }
        }
        if (!locale || locale === this.locale) {
            this.set(locale);
        } else if (this.locale.indexOf(locale + "-") === 0) {
            // current locale is regional variation of added strings
            // call set so newly added strings are used where appropriate
            this.set(this.locale);
        }
    },

    /** api: method[set]
     * :arg locale: ''String'' Language identifier tag following recommendations
     *     at http://www.ietf.org/rfc/rfc3066.txt.
     *
     * Set the language for all GeoExt components.  This will use any localized
     * strings in the dictionary (set with the :meth:`add` method) that
     * correspond to the complete matching language tag or any "higher order"
     * tag (e.g. setting "en-CA" will use strings from the "en" dictionary if
     * matching strings are not found in the "en-CA" dictionary).
     */
    set: function(locale) {
        // compile lookup based on primary and all subtags
        var tags = locale ? locale.split("-") : [];
        var id = "";
        var lookup = {}, parent;
        for (var i=0, ii=tags.length; i<ii; ++i) {
            id += (id && "-" || "") + tags[i];
            if (id in this.dict) {
                parent = this.dict[id];
                for (var str in parent) {
                    if (str in lookup) {
                        Ext.apply(lookup[str], parent[str]);
                    } else {
                        lookup[str] = Ext.apply({}, parent[str]);
                    }
                }
            }
        }

        // now extend all objects given by dot delimited names in lookup
        for (var str in lookup) {
            var obj = window;
            var parts = str.split(".");
            var missing = false;
            for (var i=0, ii=parts.length; i<ii; ++i) {
                var name = parts[i];
                if (name in obj) {
                    obj = obj[name];
                } else {
                    missing = true;
                    break;
                }
            }
            if (!missing) {
                Ext.apply(obj, lookup[str]);
            }
        }
        this.locale = locale;
        this.fireEvent("localize", locale);
    }
}))();

/* ======================================================================
    locale/en.js
   ====================================================================== */

/**
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("en", {

    "gxp.menu.LayerMenu.prototype": {
        layerText: "Layer"
    },

    "gxp.plugins.AddLayers.prototype": {
        addActionMenuText: "Add layers",
        addActionTip: "Add layers",
        addServerText: "Add a New Server",
        addButtonText: "Add layers",
        untitledText: "Untitled",
        addLayerSourceErrorText: "Error getting WMS capabilities ({msg}).\nPlease check the url and try again.",
        availableLayersText: "Available Layers",
        expanderTemplateText: "<p><b>Abstract:</b> {abstract}</p>",
        panelTitleText: "Title",
        layerSelectionText: "View available data from:",
        doneText: "Done",
        uploadText: "Upload layers"
    },
    
    "gxp.plugins.BingSource.prototype": {
        title: "Bing Layers",
        roadTitle: "Bing Roads",
        aerialTitle: "Bing Aerial",
        labeledAerialTitle: "Bing Aerial With Labels"
    },

    "gxp.plugins.FeatureEditor.prototype": {
        splitButtonText: "Edit",
        createFeatureActionText: "Create",
        editFeatureActionText: "Modify",
        createFeatureActionTip: "Create a new feature",
        editFeatureActionTip: "Edit existing feature"
    },
    
    "gxp.plugins.FeatureGrid.prototype": {
        displayFeatureText: "Display on map",
        firstPageTip: "First page",
        previousPageTip: "Previous page",
        zoomPageExtentTip: "Zoom to page extent",
        nextPageTip: "Next page",
        lastPageTip: "Last page",
        totalMsg: "Features {1} to {2} of {0}"
    },

    "gxp.plugins.GoogleEarth.prototype": {
        menuText: "3D Viewer",
        tooltip: "Switch to 3D Viewer"
    },
    
    "gxp.plugins.GoogleSource.prototype": {
        title: "Google Layers",
        roadmapAbstract: "Show street map",
        satelliteAbstract: "Show satellite imagery",
        hybridAbstract: "Show imagery with street names",
        terrainAbstract: "Show street map with terrain"
    },

    "gxp.plugins.LayerProperties.prototype": {
        menuText: "Layer Properties",
        toolTip: "Layer Properties"
    },
    
    "gxp.plugins.LayerTree.prototype": {
        shortTitle: "Layers",
        rootNodeText: "Layers",
        overlayNodeText: "Overlays",
        baseNodeText: "Base Layers"
    },

    "gxp.plugins.Legend.prototype": {
        menuText: "Show legend",
        tooltip: "Show legend"
    },

    "gxp.plugins.LoadingIndicator.prototype": {
        loadingMapMessage: "Loading map..."
    },

    "gxp.plugins.MapBoxSource.prototype": {
        title: "MapBox Layers",
        blueMarbleTopoBathyJanTitle: "Blue Marble Topography & Bathymetry (January)",
        blueMarbleTopoBathyJulTitle: "Blue Marble Topography & Bathymetry (July)",
        blueMarbleTopoJanTitle: "Blue Marble Topography (January)",
        blueMarbleTopoJulTitle: "Blue Marble Topography (July)",
        controlRoomTitle: "Control Room",
        geographyClassTitle: "Geography Class",
        naturalEarthHypsoTitle: "Natural Earth Hypsometric",
        naturalEarthHypsoBathyTitle: "Natural Earth Hypsometric & Bathymetry",
        naturalEarth1Title: "Natural Earth I",
        naturalEarth2Title: "Natural Earth II",
        worldDarkTitle: "World Dark",
        worldLightTitle: "World Light",
        worldPrintTitle: "World Print"
    },

    "gxp.plugins.Measure.prototype": {
        buttonText: "Measure",
        lengthMenuText: "Length",
        areaMenuText: "Area",
        lengthTooltip: "Measure length",
        areaTooltip: "Measure area",
        measureTooltip: "Measure"
    },

    "gxp.plugins.Navigation.prototype": {
        menuText: "Pan map",
        tooltip: "Pan map"
    },

    "gxp.plugins.NavigationHistory.prototype": {
        previousMenuText: "Zoom to previous extent",
        nextMenuText: "Zoom to next extent",
        previousTooltip: "Zoom to previous extent",
        nextTooltip: "Zoom to next extent"
    },

    "gxp.plugins.OSMSource.prototype": {
        title: "OpenStreetMap Layers",
        mapnikAttribution: "&copy; <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors",
        osmarenderAttribution: "Data CC-By-SA by <a href='http://openstreetmap.org/'>OpenStreetMap</a>"
    },

    "gxp.plugins.Print.prototype": {
        buttonText:"Print",
        menuText: "Print map",
        tooltip: "Print map",
        previewText: "Print Preview",
        notAllNotPrintableText: "Not All Layers Can Be Printed",
        nonePrintableText: "None of your current map layers can be printed"
    },

    "gxp.plugins.MapQuestSource.prototype": {
        title: "MapQuest Layers",
        osmAttribution: "Tiles Courtesy of <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a> <img src='http://developer.mapquest.com/content/osm/mq_logo.png' border='0'>",
        osmTitle: "MapQuest OpenStreetMap",
        naipAttribution: "Tiles Courtesy of <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a> <img src='http://developer.mapquest.com/content/osm/mq_logo.png' border='0'>",
        naipTitle: "MapQuest Imagery"
    },

    "gxp.plugins.QueryForm.prototype": {
        queryActionText: "Query",
        queryMenuText: "Query layer",
        queryActionTip: "Query the selected layer",
        queryByLocationText: "Query by current map extent",
        queryByAttributesText: "Query by attributes",
        queryMsg: "Querying...",
        cancelButtonText: "Cancel",
        noFeaturesTitle: "No Match",
        noFeaturesMessage: "Your query did not return any results."
    },

    "gxp.plugins.RemoveLayer.prototype": {
        removeMenuText: "Remove layer",
        removeActionTip: "Remove layer"
    },
    
    "gxp.plugins.Styler.prototype": {
        menuText: "Layer Styles",
        tooltip: "Layer Styles"

    },

    "gxp.plugins.WMSGetFeatureInfo.prototype": {
        buttonText:"Identify",
        infoActionTip: "Get Feature Info",
        popupTitle: "Feature Info"
    },

    "gxp.plugins.Zoom.prototype": {
        zoomMenuText: "Zoom box",
        zoomInMenuText: "Zoom in",
        zoomOutMenuText: "Zoom out",
        zoomTooltip: "Zoom by dragging a box",
        zoomInTooltip: "Zoom in",
        zoomOutTooltip: "Zoom out"
    },
    
    "gxp.plugins.ZoomToExtent.prototype": {
        menuText: "Zoom to max extent",
        tooltip: "Zoom to max extent"
    },
    
    "gxp.plugins.ZoomToDataExtent.prototype": {
        menuText: "Zoom to layer extent",
        tooltip: "Zoom to layer extent"
    },

    "gxp.plugins.ZoomToLayerExtent.prototype": {
        menuText: "Zoom to layer extent",
        tooltip: "Zoom to layer extent"
    },
    
    "gxp.plugins.ZoomToSelectedFeatures.prototype": {
        menuText: "Zoom to selected features",
        tooltip: "Zoom to selected features"
    },

    "gxp.FeatureEditPopup.prototype": {
        closeMsgTitle: "Save Changes?",
        closeMsg: "This feature has unsaved changes. Would you like to save your changes?",
        deleteMsgTitle: "Delete Feature?",
        deleteMsg: "Are you sure you want to delete this feature?",
        editButtonText: "Edit",
        editButtonTooltip: "Make this feature editable",
        deleteButtonText: "Delete",
        deleteButtonTooltip: "Delete this feature",
        cancelButtonText: "Cancel",
        cancelButtonTooltip: "Stop editing, discard changes",
        saveButtonText: "Save",
        saveButtonTooltip: "Save changes"
    },
    
    "gxp.FillSymbolizer.prototype": {
        fillText: "Fill",
        colorText: "Color",
        opacityText: "Opacity"
    },
    
    "gxp.FilterBuilder.prototype": {
        builderTypeNames: ["any", "all", "none", "not all"],
        preComboText: "Match",
        postComboText: "of the following:",
        addConditionText: "add condition",
        addGroupText: "add group",
        removeConditionText: "remove condition"
    },
    
    "gxp.grid.CapabilitiesGrid.prototype": {
        nameHeaderText : "Name",
        titleHeaderText : "Title",
        queryableHeaderText : "Queryable",
        layerSelectionLabel: "View available data from:",
        layerAdditionLabel: "or add a new server.",
        expanderTemplateText: "<p><b>Abstract:</b> {abstract}</p>"
    },
    
    "gxp.PointSymbolizer.prototype": {
        graphicCircleText: "circle",
        graphicSquareText: "square",
        graphicTriangleText: "triangle",
        graphicStarText: "star",
        graphicCrossText: "cross",
        graphicXText: "x",
        graphicExternalText: "external",
        urlText: "URL",
        opacityText: "opacity",
        symbolText: "Symbol",
        sizeText: "Size",
        rotationText: "Rotation"
    },

    "gxp.QueryPanel.prototype": {
        queryByLocationText: "Query by location",
        currentTextText: "Current extent",
        queryByAttributesText: "Query by attributes",
        layerText: "Layer"
    },
    
    "gxp.RulePanel.prototype": {
        scaleSliderTemplate: "{scaleType} Scale 1:{scale}",
        labelFeaturesText: "Label Features",
        labelsText: "Labels",
        basicText: "Basic",
        advancedText: "Advanced",
        limitByScaleText: "Limit by scale",
        limitByConditionText: "Limit by condition",
        symbolText: "Symbol",
        nameText: "Name"
    },
    
    "gxp.ScaleLimitPanel.prototype": {
        scaleSliderTemplate: "{scaleType} Scale 1:{scale}",
        minScaleLimitText: "Min scale limit",
        maxScaleLimitText: "Max scale limit"
    },
    
    "gxp.StrokeSymbolizer.prototype": {
        solidStrokeName: "solid",
        dashStrokeName: "dash",
        dotStrokeName: "dot",
        titleText: "Stroke",
        styleText: "Style",
        colorText: "Color",
        widthText: "Width",
        opacityText: "Opacity"
    },
    
    "gxp.StylePropertiesDialog.prototype": {   
        titleText: "General",
        nameFieldText: "Name",
        titleFieldText: "Title",
        abstractFieldText: "Abstract"
    },
    
    "gxp.TextSymbolizer.prototype": {
        labelValuesText: "Label values",
        haloText: "Halo",
        sizeText: "Size"
    },
    
    "gxp.WMSLayerPanel.prototype": {
        aboutText: "About",
        titleText: "Title",
        nameText: "Name",
        descriptionText: "Description",
        displayText: "Display",
        opacityText: "Opacity",
        formatText: "Format",
        transparentText: "Transparent",
        cacheText: "Cache",
        cacheFieldText: "Use cached version",
        stylesText: "Styles",
        infoFormatText: "Info format",
        infoFormatEmptyText: "Select a format"
    },

    "gxp.EmbedMapDialog.prototype": {
        publishMessage: "Your map is ready to be published to the web! Simply copy the following HTML to embed the map in your website:",
        heightLabel: 'Height',
        widthLabel: 'Width',
        mapSizeLabel: 'Map Size',
        miniSizeLabel: 'Mini',
        smallSizeLabel: 'Small',
        premiumSizeLabel: 'Premium',
        largeSizeLabel: 'Large'
    },
    
    "gxp.WMSStylesDialog.prototype": {
         addStyleText: "Add",
         addStyleTip: "Add a new style",
         chooseStyleText: "Choose style",
         deleteStyleText: "Remove",
         deleteStyleTip: "Delete the selected style",
         editStyleText: "Edit",
         editStyleTip: "Edit the selected style",
         duplicateStyleText: "Duplicate",
         duplicateStyleTip: "Duplicate the selected style",
         addRuleText: "Add",
         addRuleTip: "Add a new rule",
         newRuleText: "New Rule",
         deleteRuleText: "Remove",
         deleteRuleTip: "Delete the selected rule",
         editRuleText: "Edit",
         editRuleTip: "Edit the selected rule",
         duplicateRuleText: "Duplicate",
         duplicateRuleTip: "Duplicate the selected rule",
         cancelText: "Cancel",
         saveText: "Save",
         styleWindowTitle: "User Style: {0}",
         ruleWindowTitle: "Style Rule: {0}",
         stylesFieldsetTitle: "Styles",
         rulesFieldsetTitle: "Rules"
    },

    "gxp.LayerUploadPanel.prototype": {
        titleLabel: "Title",
        titleEmptyText: "Layer title",
        abstractLabel: "Description",
        abstractEmptyText: "Layer description",
        fileLabel: "Data",
        fieldEmptyText: "Browse for data archive...",
        uploadText: "Upload",
        waitMsgText: "Uploading your data...",
        invalidFileExtensionText: "File extension must be one of: ",
        optionsText: "Options",
        workspaceLabel: "Workspace",
        workspaceEmptyText: "Default workspace",
        dataStoreLabel: "Store",
        dataStoreEmptyText: "Create new store",
        defaultDataStoreEmptyText: "Default data store"
    },
    
    "gxp.NewSourceDialog.prototype": {
        title: "Add new server...",
        cancelText: "Cancel",
        addServerText: "Add Server",
        invalidURLText: "Enter a valid URL to a WMS endpoint (e.g. http://example.com/geoserver/wms)",
        contactingServerText: "Contacting Server..."
    },

    "gxp.ScaleOverlay.prototype": { 
        zoomLevelText: "Zoom level"
    }

});
/* ======================================================================
    Ext/src/locale/ext-lang-en.js
   ====================================================================== */

/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * English Translations
 * updated to 2.2 by Condor (8 Aug 2008)
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Loading...</div>';

if(Ext.data.Types){
    Ext.data.Types.stripRe = /[\$,%]/g;
}

if(Ext.DataView){
  Ext.DataView.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} selected row{1}";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Loading...";
}

Date.monthNames = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  May : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Nov : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

Date.parseCodes.S.s = "(?:st|nd|rd|th)";

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "OK",
    cancel : "Cancel",
    yes    : "Yes",
    no     : "No"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "m/d/Y");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Today",
    minText           : "This date is before the minimum date",
    maxText           : "This date is after the maximum date",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'Next Month (Control+Right)',
    prevText          : 'Previous Month (Control+Left)',
    monthYearText     : 'Choose a month (Control+Up/Down to move years)',
    todayTip          : "{0} (Spacebar)",
    format            : "m/d/y",
    okText            : "&#160;OK&#160;",
    cancelText        : "Cancel",
    startDay          : 0
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Page",
    afterPageText  : "of {0}",
    firstText      : "First Page",
    prevText       : "Previous Page",
    nextText       : "Next Page",
    lastText       : "Last Page",
    refreshText    : "Refresh",
    displayMsg     : "Displaying {0} - {1} of {2}",
    emptyMsg       : 'No data to display'
  });
}

if(Ext.form.BasicForm){
    Ext.form.BasicForm.prototype.waitTitle = "Please Wait..."
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "The value in this field is invalid";
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "The minimum length for this field is {0}",
    maxLengthText : "The maximum length for this field is {0}",
    blankText     : "This field is required",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    decimalSeparator : ".",
    decimalPrecision : 2,
    minText : "The minimum value for this field is {0}",
    maxText : "The maximum value for this field is {0}",
    nanText : "{0} is not a valid number"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "Disabled",
    disabledDatesText : "Disabled",
    minText           : "The date in this field must be after {0}",
    maxText           : "The date in this field must be before {0}",
    invalidText       : "{0} is not a valid date - it must be in the format {1}",
    format            : "m/d/y",
    altFormats        : "m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d",
    startDay          : 0
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Loading...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : 'This field should be an e-mail address in the format "user@example.com"',
    urlText      : 'This field should be a URL in the format "http:/'+'/www.example.com"',
    alphaText    : 'This field should only contain letters and _',
    alphanumText : 'This field should only contain letters, numbers and _'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Please enter the URL for the link:',
    buttonTips : {
      bold : {
        title: 'Bold (Ctrl+B)',
        text: 'Make the selected text bold.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Italic (Ctrl+I)',
        text: 'Make the selected text italic.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Underline (Ctrl+U)',
        text: 'Underline the selected text.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Grow Text',
        text: 'Increase the font size.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Shrink Text',
        text: 'Decrease the font size.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Text Highlight Color',
        text: 'Change the background color of the selected text.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Font Color',
        text: 'Change the color of the selected text.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Align Text Left',
        text: 'Align text to the left.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Center Text',
        text: 'Center text in the editor.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Align Text Right',
        text: 'Align text to the right.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Bullet List',
        text: 'Start a bulleted list.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Numbered List',
        text: 'Start a numbered list.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Hyperlink',
        text: 'Make the selected text a hyperlink.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Source Edit',
        text: 'Switch to source editing mode.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "Sort Ascending",
    sortDescText : "Sort Descending",
    columnsText  : "Columns"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(None)',
    groupByText    : 'Group By This Field',
    showGroupsText : 'Show in Groups'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Name",
    valueText  : "Value",
    dateFormat : "m/j/Y",
    trueText: "true",
    falseText: "false"
  });
}

if(Ext.grid.BooleanColumn){
   Ext.apply(Ext.grid.BooleanColumn.prototype, {
      trueText  : "true",
      falseText : "false",
      undefinedText: '&#160;'
   });
}

if(Ext.grid.NumberColumn){
    Ext.apply(Ext.grid.NumberColumn.prototype, {
        format : '0,000.00'
    });
}

if(Ext.grid.DateColumn){
    Ext.apply(Ext.grid.DateColumn.prototype, {
        format : 'm/d/Y'
    });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Drag to resize.",
    collapsibleSplitTip : "Drag to resize. Double click to hide."
  });
}

if(Ext.form.TimeField){
  Ext.apply(Ext.form.TimeField.prototype, {
    minText : "The time in this field must be equal to or after {0}",
    maxText : "The time in this field must be equal to or before {0}",
    invalidText : "{0} is not a valid time",
    format : "g:i A",
    altFormats : "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
  });
}

if(Ext.form.CheckboxGroup){
  Ext.apply(Ext.form.CheckboxGroup.prototype, {
    blankText : "You must select at least one item in this group"
  });
}

if(Ext.form.RadioGroup){
  Ext.apply(Ext.form.RadioGroup.prototype, {
    blankText : "You must select one item in this group"
  });
}
/* ======================================================================
    Styler/lang/en.js
   ====================================================================== */

/*
 * English translation file
 */
OpenLayers.Lang.en = OpenLayers.Util.extend(OpenLayers.Lang.en, {
    /* SpatialFilterPanel.js */
    "spatialfilterpanel.geometry.saved": "Geometry stored for 30 days on this browser.",
    /* Styler.js */
    "styler.delete.rule":
        "Are you sure you want to delete the ${NAME} rule?",
    "styler.feature": "Feature: ${FEATURE}",
    "styler.style": "Style: ${STYLE}",
    "styler.div.zoomlevel": "<div>{zoomType} Zoom Level: {zoom}</div>",
    "styler.div.mapzoom": "<div>Current Map Zoom: {mapZoom}</div>"
    // no trailing comma
});
/* ======================================================================
    Proj/Lang/en.js
   ====================================================================== */

OpenLayers.Util.extend(OpenLayers.Lang.en, {
    "layertree": "Themes"
});
