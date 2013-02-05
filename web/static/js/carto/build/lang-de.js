/* ======================================================================
    OpenLayers/Lang/de.js
   ====================================================================== */

/* Translators (2009 onwards):
 *  - Grille chompa
 *  - Nikiwaibel
 *  - Umherirrender
 */

/**
 * @requires OpenLayers/Lang.js
 */

/**
 * Namespace: OpenLayers.Lang["de"]
 * Dictionary for Deutsch.  Keys for entries are used in calls to
 *     <OpenLayers.Lang.translate>.  Entry bodies are normal strings or
 *     strings formatted for use with <OpenLayers.String.format> calls.
 */
OpenLayers.Lang["de"] = OpenLayers.Util.applyDefaults({

    'unhandledRequest': "Unbehandelte Anfragerückmeldung ${statusText}",

    'Permalink': "Permalink",

    'Overlays': "Overlays",

    'Base Layer': "Grundkarte",

    'noFID': "Ein Feature, für das keine FID existiert, kann nicht aktualisiert werden.",

    'browserNotSupported': "Ihr Browser unterstützt keine Vektordarstellung. Aktuell unterstützte Renderer:\n${renderers}",

    'minZoomLevelError': "Die \x3ccode\x3eminZoomLevel\x3c/code\x3e-Eigenschaft ist nur für die Verwendung mit \x3ccode\x3eFixedZoomLevels\x3c/code\x3e-untergeordneten Layers vorgesehen. Das dieser \x3ctt\x3ewfs\x3c/tt\x3e-Layer die \x3ccode\x3eminZoomLevel\x3c/code\x3e-Eigenschaft überprüft ist ein Relikt der Vergangenheit. Wir können diese Überprüfung nicht entfernen, ohne das OL basierende Applikationen nicht mehr funktionieren. Daher markieren wir es als veraltet - die \x3ccode\x3eminZoomLevel\x3c/code\x3e-Überprüfung wird in Version 3.0 entfernt werden. Bitte verwenden Sie stattdessen die Min-/Max-Lösung, wie sie unter http://trac.openlayers.org/wiki/SettingZoomLevels beschrieben ist.",

    'commitSuccess': "WFS-Transaktion: Erfolgreich ${response}",

    'commitFailed': "WFS-Transaktion: Fehlgeschlagen ${response}",

    'googleWarning': "Der Google-Layer konnte nicht korrekt geladen werden.\x3cbr\x3e\x3cbr\x3eUm diese Meldung nicht mehr zu erhalten, wählen Sie einen anderen Hintergrundlayer aus dem LayerSwitcher in der rechten oberen Ecke.\x3cbr\x3e\x3cbr\x3eSehr wahrscheinlich tritt dieser Fehler auf, weil das Skript der Google-Maps-Bibliothek nicht eingebunden wurde oder keinen gültigen API-Schlüssel für Ihre URL enthält.\x3cbr\x3e\x3cbr\x3eEntwickler: Besuche \x3ca href=\'http://trac.openlayers.org/wiki/Google\' target=\'_blank\'\x3edas Wiki\x3c/a\x3e für Hilfe zum korrekten Einbinden des Google-Layers",

    'getLayerWarning': "Der ${layerType}-Layer konnte nicht korrekt geladen werden.\x3cbr\x3e\x3cbr\x3eUm diese Meldung nicht mehr zu erhalten, wählen Sie einen anderen Hintergrundlayer aus dem LayerSwitcher in der rechten oberen Ecke.\x3cbr\x3e\x3cbr\x3eSehr wahrscheinlich tritt dieser Fehler auf, weil das Skript der \'${layerLib}\'-Bibliothek nicht eingebunden wurde.\x3cbr\x3e\x3cbr\x3eEntwickler: Besuche \x3ca href=\'http://trac.openlayers.org/wiki/${layerLib}\' target=\'_blank\'\x3edas Wiki\x3c/a\x3e für Hilfe zum korrekten Einbinden von Layern",

    'Scale = 1 : ${scaleDenom}': "Maßstab = 1 : ${scaleDenom}",

    'W': "W",

    'E': "O",

    'N': "N",

    'S': "S",

    'reprojectDeprecated': "Sie verwenden die „Reproject“-Option des Layers ${layerName}. Diese Option ist veraltet: Sie wurde entwickelt um die Anzeige von Daten auf kommerziellen Basiskarten zu unterstützen, aber diese Funktion sollte jetzt durch Unterstützung der „Spherical Mercator“ erreicht werden. Weitere Informationen sind unter http://trac.openlayers.org/wiki/SphericalMercator verfügbar.",

    'methodDeprecated': "Die Methode ist veraltet und wird in 3.0 entfernt. Bitte verwende stattdessen ${newMethod}."

});
/* ======================================================================
    FeatureEditing/resources/lang/de.js
   ====================================================================== */

OpenLayers.Util.extend(OpenLayers.Lang.de, {
    'RedLining Panel': 'Zeichnen',
    'Export KML': 'KML Export',
    "Export": "Export",
    'Import KML': 'KML Import',
    "Import": "Import",
    'Close': 'Schliessen',
    'Attributes': 'Attribute',
    "select a color...": "Farbe wählen...",
    'color': 'Farbe',
    'Style': 'Stil',
    'Delete': 'Löschen',
    'Delete feature': 'Das Objekt löschen',
    'Delete Feature': 'Das Objekt löschen',
    'Do you really want to delete this feature ?': 'Wollen Sie dieses Objekt wirklich löschen ?',
    'DeleteAll': 'Alle löschen',
    'Delete all features': 'Alle Objekte löschen',
    'Delete ALL Features': 'Alle Objekte löschen',
    'Do you really want to delete all features ?': 'Wollen Sie wirklich alle Objekte löschen ?',
    "myOption": "meine Option",
    'Edit Feature': 'Das Objekt editieren',
    "Point": "Punkt",
    "Circle": "Kreis",
    "Box": "Rechteck",
    "LineString": "Linie",
    "Polygon": "Fläche",
    "Label": "Text",
    "MultiPoint": "mehrere Punkte",
    "MultiLineString": "mehrere Linien",
    "MultiPolygon": "mehrere Flächen",
    "Create point": "Punkt zeichnen",
    "Create line": "Linie zeichnen",
    "Create circle": "Kreis zeichnen",
    "Create box": "Rechteck zeichnen",
    "Create polygon": "Fläche zeichnen",
    "Create label": "Text erstellen",

     /* colors for styler */
    "black": "schwarz",
    "blue": "blau",
    "brown": "braun",
    "cyan": "hellblau",
    "gold": "goldig",
    "gray": "grau",
    "green": "grün",
    "indigo": "dunkelviolett",
    "lime": "hellgrün",
    "magenta": "purpurrot",
    "maroon": "rötlich braun",
    "olive": "olivengrün",
    "orange": "orange",
    "pink": "rosarot",
    "plum": "hellviolett",
    "purple": "violett",
    "red": "rot",
    "salmon": "lachsrosa",
    "silver": "silber",
    "white": "weiss",
    "yellow": "gelb"
});
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
    CGXP/locale/de.js
   ====================================================================== */

/*
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("de", {
    "cgxp.plugins.Measure.prototype": {
        pointMenuText: "Punkt",
        pointTooltip: "Punkt messen",
        lengthMenuText: "Länge",
        areaMenuText: "Fläche",
        azimuthMenuText: "Azimut",
        coordinateText: "Koordinaten: ",
        easternText: "Östlich: ",
        northernText: "Nordlich: ",
        distanceText: "Distanz: ",
        azimuthText: "Azimut: ",
        lengthTooltip: "Länge messen",
        areaTooltip: "Fläche messen",
        azimuthTooltip: "Azimut messen",
        measureTooltip: "Messen"
    },

    "cgxp.plugins.FullTextSearch.prototype": {
        tooltipTitle: "Suchen",
        emptyText: "Suche Ort, Objekte...",
        loadingText: "Suchen..."
    },

    "cgxp.plugins.ThemeFinder.prototype": {
        emptyText: "Themen- oder Layername eingeben"
    },

    "cgxp.plugins.ThemeSelector.prototype": {
        localTitle: "Lokale Layer",
        externalTitle: "Externe Layer",
        toolTitle: "Themen"
    },

    "cgxp.plugins.Permalink.prototype": {
        toolTitle: "Permalink",
        windowTitle: "Die aktuelle Seite kann mit folgender URL aufgerufen werden:",
        openlinkText: "Link in neuem Tab öffnen",
        closeText: "Schliessen",
        incompatibleWithIeText: "Achtung: diese URL ist zu lang für Microsoft Internet Explorer!",
        menuText: 'Permalink'
    },

    "cgxp.plugins.FeatureGrid.prototype": {
        clearAllText: "Resultate löschen",
        selectText: "Auswahl",
        selectAllText: "Alle",
        selectNoneText: "Keine",
        selectToggleText: "Umkehren",
        actionsText: "Auf Auswahl anwenden",
        zoomToSelectionText: "Zentrieren auf Ausdehnung",
        csvSelectionExportText: "Als CSV Datei exportieren",
        maxFeaturesText: "Maximale Anzahl Resultate erreicht",
        resultText: "Resultat",
        resultsText: "Resultate",
        suggestionText: "Tipp"
    },

    "cgxp.plugins.FeaturesWindow.prototype": {
        suggestionText: "Tipp"
    },

    "cgxp.plugins.Print.prototype": {
        printTitle: "Drucken",
        titlefieldText: "Titel",
        titlefieldvalueText: "Kartentitel",
        includelegendText: "Legende anzeigen",
        layoutText: "Layout",
        commentfieldText: "Kommentar",
        commentfieldvalueText: "Kommentar auf der Karte",
        dpifieldText: "Auflösung",
        scalefieldText: "Massstab",
        rotationfieldText: "Rotation",
        printbuttonText: "Drucken",
        printbuttonTooltip: "Drucken",
        exportpngbuttonText: "Export in PNG",
        waitingText: "PDF Generierung...",
        downloadText: 'Download',
        readyText: "PDF ist bereit.",
        failureTitle: "Fehler beim Drucken",
        failureText: "Es ist ein Fehler aufgetreten beim Drucken. Bitte prüfen Sie die Parameter."
    },

    "cgxp.plugins.Login.prototype": {
        authenticationFailureText: "Benutzername oder Passwort fehlerhaft. Bitte geben Sie Ihre Daten erneut ein.",
        loggedAsText: "Angemeldet als <b>${user}</b>",
        logoutText: "Abmelden",
        loginText: "Anmelden",
        usernameText: "Benutzername",
        passwordText: "Passwort"
    },

    "cgxp.plugins.Help.prototype": {
        helpactiontooltipText: "Hilfe",
        menuText: "Hilfe"
    },

    "cgxp.plugins.Redlining.prototype": {
        redliningText: "Zeichnen",
        attributesText: 'Attribute'
    },

    "cgxp.MapOpacitySlider.prototype": {
        orthoText: "Orthofoto"
    },

    "cgxp.plugins.Legend.prototype": {
        legendbuttonText: "Legende",
        legendbuttonTooltip: "Legende anzeigen",
        legendwindowTitle: "Legende"
    },

    "cgxp.plugins.ScaleChooser.prototype": {
        labelText: "Massstab: "
    },

    "cgxp.plugins.Editing.prototype": {
        helpText: "Klicken Sie auf die Karten um <b>ein bestehendes Element zu editieren</b>, oder",
        layerMenuText: "Wählen Sie eine Ebene",
        createBtnText: "Erstellen Sie ein neues Element",
        forbiddenText: "Diese Aktion ist nicht erlaubt!"
    },

    "cgxp.tree.LayerTree.prototype": {
        moveupText: "Nach oben",
        movedownText: "Nach unten",
        moreinfoText: "Mehr Information",
        deleteText: "Layer löschen",
        opacityText: "Layertransparenz anpassen",
        zoomtoscaleText: "Der Layer ist nicht sichtbar in diesem Massstab.",
        opacitylabelText: "Transparenz",
        showhidelegendText: "Legende anzeigen/verstecken",
        themealreadyloadedText: "Dieses Thema ist schon geladen.",
        showIn3dText: 'Ansicht in 3D'
    },

    "cgxp.plugins.FeaturesWindow.prototype": {
        windowTitleText: 'Resultate',
        itemsText: "elemente",
        itemText: "element"
    },

    "cgxp.plugins.WMSBrowser.prototype": {
        buttonText: 'WMS hinzufügen',
        windowTitleText: 'WMS-Layer hineinladen',
        menuText: 'WMS hinzufügen'
    },

    "cgxp.plugins.AddKMLFile.prototype": {
        buttonText: "KML hinzufügen",
        waitMsgText: "Lade Daten..."
    },

    "cgxp.plugins.ContextualData.prototype": {
        actionTooltipText: "Contextual Informations Tooltips",
        menuText: 'Contextual Informations'
    },

    "cgxp.plugins.ContextualData.Control.prototype": {
        streetviewLabelText: 'StreetView Link',
        userValueErrorText: 'The value returned by the handleServerData methode ' +
        'must be an object. See the example in the API.',
        userValueErrorTitleText: 'Error'
    },

    "cgxp.plugins.ContextualData.Tooltip.prototype": {
        popupTitleText: "Location",
        defaultTpl: "Schweizer Koordinaten: {coord_x} {coord_y}<br />" +
            "WGS 84: {wsg_x} {wsg_y}<br />",
        defaultTplElevation: "Elevation (Terrain): {elevation_dtm} [m]<br />" +
            "Elevation (Surface): {elevation_dsm} [m]<br />" +
            "Height (Surface-Terrain): {elevation_dhm} [m]<br />"
    },

    "cgxp.plugins.ContextualData.ContextPopup.prototype": {
        popupTitleText: "Location",
        coordTpl: "<tr><td width=\"150\">Schweizer Koordinaten</td>" +
            "<td>{coord_x} {coord_y}</td></tr>" +
            "<tr><td>WGS 84</td><td>{wsg_x} {wsg_y}</td></tr>",
        elevationTpl: "<tr><td>Elevation (Terrain)</td><td>{elevation_dtm} [m]</td></tr>" +
            "<tr><td>Elevation (Surface)</td><td>{elevation_dsm} [m]</td></tr>" +
            "<tr><td>Height (Surface-Terrain)</td><td>{elevation_dhm} [m]</td></tr>" +
            "<tr><td>Slope</td><td>{elevation_slope} [°]</td></tr>"
    },

    "cgxp.plugins.Profile.prototype": {
        helpText: "<h1>Höhenprofil</h1>Zeichnen Sie eine Linie auf der Karte. Doppelklicken Sie um die Linie zu beenden und das Höhenprofil anzuzeigen.",
        waitMsgText: "Höhenprofil wird geladen...",
        xLabelText: "Abstand (m)",
        yLabelText: "Höhe (m)",
        errorMsg: "Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.",
        exportCsvText: "Export als CSV",
        menuText: 'Höhenprofil'
    },

    "cgxp.plugins.GoogleEarth.prototype": {
        tooltipText: 'GoogleEarth',
        menuText: 'GoogleEarth'
    },

    "cgxp.plugins.StreetView.prototype": {
        tooltipText: 'StreetView',
        menuText: 'StreetView'
    },

    "cgxp.plugins.QueryBuilder.prototype": {
        layerText: "Layer",
        querierText: "Attributabfrage",
        loadingText: 'Lade Daten...',
        incompleteFormText: 'Bitte alle erforderlichen Felder ausfüllen.',
        noResultText: 'Es wurde kein Resultat gefunden.',
        queryButtonText: 'Abfrage',
        errorText: 'Bitte alle erforderlichen Felder ausfüllen.',
        noGeomFieldError: 'Es wurde kein Geometriefeld in dieser Ebene gefunden.'
    },

    "cgxp.plugins.MapQuery.prototype": {
        actionTooltip: 'Informationen in der Karte abfragen',
        menuText: 'Informationen abfragen'
    },

    "cgxp.plugins.WFSGetFeature.prototype": {
        actionTooltip: 'Informationen in der Karte abfragen'
    },

    "cgxp.plugins.WMSGetFeatureInfo.prototype": {
        actionTooltip: 'Informationen in der Karte abfragen',
        noLayerSelectedMessage: 'Keine Ebene ausgewählt'
    },

    "cgxp.plugins.GetFeature.prototype": {
        actionTooltip: 'Informationen in der Karte abfragen',
        menuText: 'Abfragen der Karte',
        unqueriedLayerTitle: 'Diese Ebene kann nicht abgefragt werden.',
        unqueriedLayerText: "Diese Ebene unterstützt nur Punkt-Abfragen.",
        queryResultMessage: "Informationen in einem Rechteck können mit der " +
            "{key} Taste abgefragt werden."
    },

    "GeoExt.ux.form.FeaturePanel.prototype": {
        pointRadiusFieldText: "Grösse",
        colorFieldText: "Farbe",
        strokeWidthFieldText: "Breite",
        labelFieldText: "Text",
        fontSizeFieldText: "Grösse"
    }
});
/* ======================================================================
    Proj/Lang/GeoExt-de.js
   ====================================================================== */

/*
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("de", {
});
/* ======================================================================
    Ext/src/locale/ext-lang-de.js
   ====================================================================== */

/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
/*
 * German translation
 * 2007-Apr-07 update by schmidetzki and humpdi
 * 2007-Oct-31 update by wm003
 * 2009-Jul-10 update by Patrick Matsumura and Rupert Quaderer
 * 2010-Mar-10 update by Volker Grabsch
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Übertrage Daten ...</div>';

if(Ext.View){
   Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
   Ext.grid.GridPanel.prototype.ddText = "{0} Zeile(n) ausgewählt";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Diesen Tab schließen";
}

if(Ext.form.BasicForm){
   Ext.form.BasicForm.prototype.waitTitle = "Bitte warten...";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Der Wert des Feldes ist nicht korrekt";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Übertrage Daten...";
}

Date.monthNames = [
   "Januar",
   "Februar",
   "März",
   "April",
   "Mai",
   "Juni",
   "Juli",
   "August",
   "September",
   "Oktober",
   "November",
   "Dezember"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Jan : 0,
  Feb : 1,
  "M\u00e4r" : 2,
  Apr : 3,
  Mai : 4,
  Jun : 5,
  Jul : 6,
  Aug : 7,
  Sep : 8,
  Okt : 9,
  Nov : 10,
  Dez : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
   "Sonntag",
   "Montag",
   "Dienstag",
   "Mittwoch",
   "Donnerstag",
   "Freitag",
   "Samstag"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Abbrechen",
      yes    : "Ja",
      no     : "Nein"
   };
}

if(Ext.util.Format){
    Ext.util.Format.__number = Ext.util.Format.number;
    Ext.util.Format.number = function(v, format) {
        return Ext.util.Format.__number(v, format || "0.000,00/i");
    };

   Ext.util.Format.date = function(v, format) {
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "d.m.Y");
   };
}

if(Ext.DatePicker){
   Ext.apply(Ext.DatePicker.prototype, {
      todayText         : "Heute",
      minText           : "Dieses Datum liegt von dem erstmöglichen Datum",
      maxText           : "Dieses Datum liegt nach dem letztmöglichen Datum",
      disabledDaysText  : "",
      disabledDatesText : "",
      monthNames        : Date.monthNames,
      dayNames          : Date.dayNames,
      nextText          : "Nächster Monat (Strg/Control + Rechts)",
      prevText          : "Vorheriger Monat (Strg/Control + Links)",
      monthYearText     : "Monat auswählen (Strg/Control + Hoch/Runter, um ein Jahr auszuwählen)",
      todayTip          : "Heute ({0}) (Leertaste)",
      format            : "d.m.Y",
      okText            : "&#160;OK&#160;",
      cancelText        : "Abbrechen",
      startDay          : 1
   });
}

if(Ext.PagingToolbar){
   Ext.apply(Ext.PagingToolbar.prototype, {
      beforePageText : "Seite",
      afterPageText  : "von {0}",
      firstText      : "Erste Seite",
      prevText       : "vorherige Seite",
      nextText       : "nächste Seite",
      lastText       : "letzte Seite",
      refreshText    : "Aktualisieren",
      displayMsg     : "Anzeige Eintrag {0} - {1} von {2}",
      emptyMsg       : "Keine Daten vorhanden"
   });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Bitte geben Sie mindestens {0} Zeichen ein",
      maxLengthText : "Bitte geben Sie maximal {0} Zeichen ein",
      blankText     : "Dieses Feld darf nicht leer sein",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Der Mindestwert für dieses Feld ist {0}",
      maxText : "Der Maximalwert für dieses Feld ist {0}",
      nanText : "{0} ist keine Zahl",
      decimalSeparator : ","
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "nicht erlaubt",
      disabledDatesText : "nicht erlaubt",
      minText           : "Das Datum in diesem Feld muss nach dem {0} liegen",
      maxText           : "Das Datum in diesem Feld muss vor dem {0} liegen",
      invalidText       : "{0} ist kein gültiges Datum - es muss im Format {1} eingegeben werden",
      format            : "d.m.Y",
      altFormats        : "j.n.Y|j.n.y|j.n.|j.|j/n/Y|j/n/y|j-n-y|j-n-Y|j/n|j-n|dm|dmy|dmY|j|Y-n-j",
      startDay          : 1
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "Lade Daten ...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Dieses Feld sollte eine E-Mail-Adresse enthalten. Format: "user@example.com"',
      urlText      : 'Dieses Feld sollte eine URL enthalten. Format: "http:/'+'/www.example.com"',
      alphaText    : 'Dieses Feld darf nur Buchstaben enthalten und _',
      alphanumText : 'Dieses Feld darf nur Buchstaben und Zahlen enthalten und _'
   });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Bitte geben Sie die URL für den Link ein:',
    buttonTips : {
      bold : {
        title: 'Fett (Ctrl+B)',
        text: 'Erstellt den ausgewählten Text in Fettschrift.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Kursiv (Ctrl+I)',
        text: 'Erstellt den ausgewählten Text in Schrägschrift.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Unterstrichen (Ctrl+U)',
        text: 'Unterstreicht den ausgewählten Text.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Text vergößern',
        text: 'Erhöht die Schriftgröße.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Text verkleinern',
        text: 'Verringert die Schriftgröße.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Text farblich hervorheben',
        text: 'Hintergrundfarbe des ausgewählten Textes ändern.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Schriftfarbe',
        text: 'Farbe des ausgewählten Textes ändern.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Linksbündig',
        text: 'Setzt den Text linksbündig.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Zentrieren',
        text: 'Zentriert den Text in Editor.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Rechtsbündig',
        text: 'Setzt den Text rechtsbündig.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Aufzählungsliste',
        text: 'Beginnt eine Aufzählungsliste mit Spiegelstrichen.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Numerierte Liste',
        text: 'Beginnt eine numerierte Liste.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Hyperlink',
        text: 'Erstellt einen Hyperlink aus dem ausgewählten text.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Source bearbeiten',
        text: 'Zur Bearbeitung des Quelltextes wechseln.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Aufsteigend sortieren",
      sortDescText : "Absteigend sortieren",
      lockText     : "Spalte sperren",
      unlockText   : "Spalte freigeben (entsperren)",
      columnsText  : "Spalten"
   });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Keine)',
    groupByText    : 'Dieses Feld gruppieren',
    showGroupsText : 'In Gruppen anzeigen'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
      nameText   : "Name",
      valueText  : "Wert",
      dateFormat : "d.m.Y"
  });
}

if(Ext.grid.BooleanColumn){
   Ext.apply(Ext.grid.BooleanColumn.prototype, {
      trueText  : "wahr",
      falseText : "falsch"
   });
}

if(Ext.grid.NumberColumn){
    Ext.apply(Ext.grid.NumberColumn.prototype, {
        format : '0.000,00/i'
    });
}

if(Ext.grid.DateColumn){
    Ext.apply(Ext.grid.DateColumn.prototype, {
        format : 'd.m.Y'
    });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "Ziehen, um Größe zu ändern.",
    collapsibleSplitTip : "Ziehen, um Größe zu ändern. Doppelklick um Panel auszublenden."
  });
}

if(Ext.form.TimeField){
   Ext.apply(Ext.form.TimeField.prototype, {
    minText : "Die Zeit muss gleich oder nach {0} liegen",
    maxText : "Die Zeit muss gleich oder vor {0} liegen",
    invalidText : "{0} ist keine gültige Zeit",
    format : "H:i"
   });
}

if(Ext.form.CheckboxGroup){
  Ext.apply(Ext.form.CheckboxGroup.prototype, {
    blankText : "Du mußt mehr als einen Eintrag aus der Gruppe auswählen"
  });
}

if(Ext.form.RadioGroup){
  Ext.apply(Ext.form.RadioGroup.prototype, {
    blankText : "Du mußt einen Eintrag aus der Gruppe auswählen"
  });
}
/* ======================================================================
    Proj/Lang/de.js
   ====================================================================== */

OpenLayers.Util.extend(OpenLayers.Lang.de, {
    "layertree": "Themen"
});
/* ======================================================================
    Styler/lang/de.js
   ====================================================================== */

/*
 * German translation file
 */
OpenLayers.Lang.de = OpenLayers.Util.extend(OpenLayers.Lang.de, {
    /* SpatialComboBox.js */
    "intersects": "schneidet",
    "inside": "innerhalb",
    "contains": "beinhaltet",
    /* FilterPanel.js */
    "This field is mandatory": "Eingabe erforderlich. Es kann nach Text mit * und ? gesucht werden. z.B.",
    /* SpatialFilterPanel.js */
    "Modify geometry": "Geometrie editieren",
    "Save this geometry": "Geometrie speichern",
    "spatialfilterpanel.geometry.saved": "Die Geometrie ist für 30 Tage mit diesem Browser abgespeichert.",
    /* FilterBuilder.js */
    "any": "eine",
    "all": "alle",
    "none": "keine",
    "not all": "nicht alle",
    //"Matching": "TODO",
    //"these conditions:": "TODO",
    "Condition": "Bedingung",
    "Spatial condition": "Räumliche Bedingung",
    "Group": "Gruppe",
    "based on a point": "Punkt zeichnen",
    "based on a line": "Linie zeichnen",
    "based on a polygon": "Fläche zeichnen",
    "based on a stored geometry": "gespeicherte Geometrien",
    "Delete this condition": "Bedingung entfernen"
    // no trailing comma
    /* Styler.js */
    // TODO
    /* ColorManager.js */
    // TODO
    /* FillSymbolizer.js */
    // TODO
    /* LegendPanel.js */
    // TODO
    /* PointSymbolizer.js */
    // TODO
    /* RuleBuilder.js */
    // TODO
    /* RuleChooser.js */
    // TODO
    /* RulePanel.js */
    // TODO
    /* ScaleLimitPanel.js */
    // TODO
    /* StrokeSymbolizer.js */
    // TODO
    /* TextSymbolizer */
    // TODO
    /* ScaleSliderTip.js */
    // TODO
});
