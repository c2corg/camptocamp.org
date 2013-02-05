/* ======================================================================
    OpenLayers/Lang/fr.js
   ====================================================================== */

/* Translators (2009 onwards):
 *  - Damouns
 *  - IAlex
 */

/**
 * @requires OpenLayers/Lang.js
 */

/**
 * Namespace: OpenLayers.Lang["fr"]
 * Dictionary for Français.  Keys for entries are used in calls to
 *     <OpenLayers.Lang.translate>.  Entry bodies are normal strings or
 *     strings formatted for use with <OpenLayers.String.format> calls.
 */
OpenLayers.Lang["fr"] = OpenLayers.Util.applyDefaults({

    'unhandledRequest': "Requête non gérée, retournant ${statusText}",

    'Permalink': "Permalien",

    'Overlays': "Calques",

    'Base Layer': "Calque de base",

    'noFID': "Impossible de mettre à jour un objet sans identifiant (fid).",

    'browserNotSupported': "Votre navigateur ne supporte pas le rendu vectoriel. Les renderers actuellement supportés sont : \n${renderers}",

    'minZoomLevelError': "La propriété minZoomLevel doit seulement être utilisée pour des couches FixedZoomLevels-descendent. Le fait que cette couche WFS vérifie la présence de minZoomLevel est une relique du passé. Nous ne pouvons toutefois la supprimer sans casser des applications qui pourraient en dépendre. C\'est pourquoi nous la déprécions -- la vérification du minZoomLevel sera supprimée en version 3.0. A la place, merci d\'utiliser les paramètres de résolutions min/max tel que décrit sur : http://trac.openlayers.org/wiki/SettingZoomLevels",

    'commitSuccess': "Transaction WFS : SUCCES ${response}",

    'commitFailed': "Transaction WFS : ECHEC ${response}",

    'googleWarning': "La couche Google n\'a pas été en mesure de se charger correctement.\x3cbr\x3e\x3cbr\x3ePour supprimer ce message, choisissez une nouvelle BaseLayer dans le sélecteur de couche en haut à droite.\x3cbr\x3e\x3cbr\x3eCela est possiblement causé par la non-inclusion de la librairie Google Maps, ou alors parce que la clé de l\'API ne correspond pas à votre site.\x3cbr\x3e\x3cbr\x3eDéveloppeurs : pour savoir comment corriger ceci, \x3ca href=\'http://trac.openlayers.org/wiki/Google\' target=\'_blank\'\x3ecliquez ici\x3c/a\x3e",

    'getLayerWarning': "La couche ${layerType} n\'est pas en mesure de se charger correctement.\x3cbr\x3e\x3cbr\x3ePour supprimer ce message, choisissez une nouvelle BaseLayer dans le sélecteur de couche en haut à droite.\x3cbr\x3e\x3cbr\x3eCela est possiblement causé par la non-inclusion de la librairie ${layerLib}.\x3cbr\x3e\x3cbr\x3eDéveloppeurs : pour savoir comment corriger ceci, \x3ca href=\'http://trac.openlayers.org/wiki/${layerLib}\' target=\'_blank\'\x3ecliquez ici\x3c/a\x3e",

    'Scale = 1 : ${scaleDenom}': "Echelle ~ 1 : ${scaleDenom}",

    'W': "O",

    'E': "E",

    'N': "N",

    'S': "S",

    'reprojectDeprecated': "Vous utilisez l\'option \'reproject\' sur la couche ${layerName}. Cette option est dépréciée : Son usage permettait d\'afficher des données au dessus de couches raster commerciales.Cette fonctionalité est maintenant supportée en utilisant le support de la projection Mercator Sphérique. Plus d\'information est disponible sur http://trac.openlayers.org/wiki/SphericalMercator.",

    'methodDeprecated': "Cette méthode est dépréciée, et sera supprimée à la version 3.0. Merci d\'utiliser ${newMethod} à la place."
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
    CGXP/locale/fr.js
   ====================================================================== */

/*
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("fr", {
    "Ext.layout.FormLayout.prototype": {
        labelSeparator: "&nbsp;:"
    },

    "cgxp.plugins.Measure.prototype": {
        pointMenuText: "Point",
        pointTooltip: "Mesure de point",
        lengthMenuText: "Longueur",
        areaMenuText: "Surface",
        azimuthMenuText: "Azimut",
        coordinateText: "Coordonnées&nbsp;: ",
        easternText: "Est&nbsp;: ",
        northernText: "Nord&nbsp;: ",
        distanceText: "Distance&nbsp;: ",
        azimuthText: "Azimut&nbsp;: ",
        lengthTooltip: "Mesure de longueur",
        areaTooltip: "Mesure de surface",
        azimuthTooltip: "Mesure d'azimut",
        measureTooltip: "Mesure"
    },

   "cgxp.plugins.FullTextSearch.prototype": {
        tooltipTitle: "Rechercher",
        emptyText: "Recherche...",
        loadingText: "Recherche en cours..."
    },
    
    "cgxp.plugins.ThemeFinder.prototype": {
        emptyText: "Rechercher un thème ou une couche"
    },

    "cgxp.plugins.ThemeSelector.prototype": {
        localTitle: "Couches locales",
        externalTitle: "Couches externes",
        toolTitle: "Thèmes"
    },
    
    "cgxp.plugins.Permalink.prototype": {
        toolTitle: "Permalien",
        windowTitle: "Permalien",
        openlinkText: "Ouvrir le lien",
        closeText: "Fermer",
        incompatibleWithIeText: "Attention&nbsp;: cette URL est trop longue pour Microsoft Internet Explorer%nbsp;!",
        menuText: 'Permalien'
    },
    
    "cgxp.plugins.FeatureGrid.prototype": {
        clearAllText: "Tout effacer",
        selectText: "Sélectionner",
        selectAllText: "Tous",
        selectNoneText: "Aucun",
        selectToggleText: "Inverser la sélection",
        actionsText: "Actions sur la sélection",
        zoomToSelectionText: "Zoomer sur la sélection",
        csvSelectionExportText: "Exporter en CSV",
        maxFeaturesText: "Nombre maximum de résultats",
        resultText: "Résultat",
        resultsText: "Résultats",
        suggestionText: "Suggestion"
    },

    "cgxp.plugins.FeaturesWindow.prototype": {
        suggestionText: "Suggestion"
    },

    "cgxp.plugins.Print.prototype": {
        printTitle: "Imprimer",
        titlefieldText: "Titre",
        titlefieldvalueText: "Titre de la carte",
        commentfieldText: "Commentaires",
        commentfieldvalueText: "Commentaires sur la carte",
        includelegendText: "Inclure la légende",
        layoutText: "Format",
        dpifieldText: "Résolution",
        scalefieldText: "Échelle",
        rotationfieldText: "Rotation",
        printbuttonText: "Imprimer",
        printbuttonTooltip: "Imprimer",
        exportpngbuttonText: "Exporter en PNG",
        waitingText: "Impression...",
        downloadText: "Télécharger",
        readyText: "Votre PDF est prêt.",
        failureTitle: "Echec de l'impression",
        failureText: "L'impression a échoué. Merci de vérifier les paramètres."
    },

    "cgxp.plugins.Login.prototype": {
        authenticationFailureText: "Impossible de se connecter.",
        loggedAsText: "Connecté en tant que ${user}",
        logoutText: "Déconnexion",
        loginText: "Connexion",
        usernameText: "Nom d'utilisateur",
        passwordText: "Mot de passe"
    },

    "cgxp.plugins.Help.prototype": {
        helpactiontooltipText: "Aide",
        menuText: 'Aide'
    },

    "cgxp.plugins.Redlining.prototype": {
        redliningText: "Surlignage",
        attributesText: 'Attributs'
    },

    "cgxp.MapOpacitySlider.prototype": {
        orthoText: "Orthophoto"
    },

    "cgxp.plugins.Legend.prototype": {
        legendbuttonText: "Légende",
        legendbuttonTooltip: "Afficher la légende de la carte",
        legendwindowTitle: "Légende"
    },

    "cgxp.plugins.ScaleChooser.prototype": {
        labelText: "Échelle&nbsp;: "
    },

    "cgxp.plugins.Editing.prototype": {
        helpText: "Cliquer sur la carte pour <b>éditer des objets</b>, ou",
        layerMenuText: "Choisir une couche",
        createBtnText: "Créer un nouvel objet",
        forbiddenText: "Vous n'êtes pas autorisé à réaliser cette action&nbsp;!"
    },

    "cgxp.tree.LayerTree.prototype": {
        moveupText: "Monter",
        movedownText: "Descendre",
        moreinfoText: "Plus d'informations",
        deleteText: "Supprimer la couche",
        opacityText: "Modifier l'opacité de la couche",
        zoomtoscaleText: "Cette couche n'est pas visible à ce niveau de zoom.",
        opacitylabelText: "Opacité",
        showhidelegendText: "Afficher/masquer la légende",
        themealreadyloadedText: "Ce thème est déjà chargé.",
        showIn3dText: 'Afficher en 3D'
    },

    "cgxp.plugins.FeaturesWindow.prototype": {
        windowTitleText: 'Résultats',
        itemsText: "éléments",
        itemText: "élément"
    },

    "cgxp.plugins.WMSBrowser.prototype": {
        buttonText: "Ajouter WMS",
        windowTitleText: "Ajouter des couches WMS",
        menuText: "Ajouter WMS"
    },

    "cgxp.plugins.AddKMLFile.prototype": {
        buttonText: "Ajouter KML",
        waitMsgText: "Chargement..."
    },

    "cgxp.plugins.ContextualData.prototype": {
        actionTooltipText: "Tooltips d'informations contextuelles",
        menuText: 'Informations contextuel'
    },

    "cgxp.plugins.ContextualData.Control.prototype": {
        streetviewLabelText: 'Lien StreetView',
        userValueErrorText: "La valeur retournée par la méthode handleServerData" +
            "methode doit être un objet. Voir l'exemple dans la documentation de l'API",
        userValueErrorTitleText: 'Erreur'
    },

    "cgxp.plugins.ContextualData.Tooltip.prototype": {
        popupTitleText: "Position",
        defaultTpl: "Coordonnées suisses&nbsp;: {coord_x} {coord_y}<br />" +
            "WGS 84&nbsp;: {wsg_x} {wsg_y}<br />",
        defaultTplElevation: "Élevation (Terrain)&nbsp;: {elevation_dtm} [m]<br />" +
            "Élevation (Surface)&nbsp;: {elevation_dsm} [m]<br />" +
            "Hauteur (Surface-Terrain)&nbsp;: {elevation_dhm} [m]<br />"
    },

    "cgxp.plugins.ContextualData.ContextPopup.prototype": {
        popupTitleText: "Position",
        coordTpl: "<tr><td width=\"150\">Coordonnées suisses</td>" +
            "<td>{coord_x} {coord_y}</td></tr>" +
            "<tr><td>WGS 84</td><td>{wsg_x} {wsg_y}</td></tr>",
        elevationTpl: "<tr><td>Élevation (Terrain)</td><td>{elevation_dtm} [m]</td></tr>" +
            "<tr><td>Élevation (Surface)</td><td>{elevation_dsm} [m]</td></tr>" +
            "<tr><td>Hauteur (Surface-Terrain)</td><td>{elevation_dhm} [m]</td></tr>" +
            "<tr><td>Pente du terrain</td><td>{elevation_slope} [°]</td></tr>"
    },

    "cgxp.plugins.Profile.prototype": {
        helpText: "<h1>Profil altimétrique</h1>Dessinez une ligne sur la carte. Double-cliquez pour terminer et afficher le profil.",
        waitMsgText: "Chargement du profil altimétrique...",
        xLabelText: "Distance (m)",
        yLabelText: "Altitude (m)",
        errorMsg: "Une erreur s'est produite. Veuillez recommencer.",
        exportCsvText: "Exporter en CSV",
        tooltipText: 'Profil altimétrique',
        menuText: 'Profil altimétrique'
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
        layerText: "Couche",
        querierText: "Requêteur",
        loadingText: 'Chargement...',
        incompleteFormText: 'Formulaire incomplet.',
        noResultText: 'Pas de résultat trouvé.',
        queryButtonText: 'Effectuer la requête',
        errorText: 'Une erreur est survenue.',
        noGeomFieldError: 'Pas de champs géometrique trouvé.'
    },

    "cgxp.plugins.MapQuery.prototype": {
        actionTooltip: 'Interroge la carte',
        menuText: 'Interroge la carte'
    },

    "cgxp.plugins.WFSGetFeature.prototype": {
        actionTooltip: 'Interroge la carte'
    },

    "cgxp.plugins.WMSGetFeatureInfo.prototype": {
        actionTooltip: 'Interroge la carte',
        noLayerSelectedMessage: 'Pas de couche sélectionnée'
    },

    "cgxp.plugins.GetFeature.prototype": {
        tooltipText: 'Interroge la carte',
        menuText: 'Interroge la carte',
        unqueriedLayerTitle: "Impossible d'interroger cette couche",
        unqueriedLayerText: "Seules les interrogations par simple clic sont " + 
            "possibles pour cette couche.",
        queryResultMessage: "Utilisez la touche {key} pour faire des sélections rectangulaires."
    },

    "cgxp.FloorSlider.prototype": {
        skyText: "Ciel",
        floorText: "Étages"
    },

    "GeoExt.ux.form.FeaturePanel.prototype": {
        pointRadiusFieldText: "Taille",
        colorFieldText: "Couleur",
        strokeWidthFieldText: "Epaisseur du trait",
        labelFieldText: "Étiquette",
        fontSizeFieldText: "Taille"
    }
});
/* ======================================================================
    locale/fr.js
   ====================================================================== */

/**
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("fr", {

    "gxp.plugins.AddLayers.prototype": {
        addActionMenuText: "Ajouter des calques",
        addActionTip: "Ajouter des calques",
        addServerText: "Ajouter un nouveau serveur",
        untitledText: "Sans titre",
        addLayerSourceErrorText: "Impossible d'obtenir les capacités WMS ({msg}).\nVeuillez vérifier l'URL et essayez à nouveau.",
        availableLayersText: "Couches disponibles",
        doneText: "Terminé",
        uploadText: "Télécharger des données"
    },
    
    "gxp.plugins.BingSource.prototype": {
        title: "Calques Bing",
        roadTitle: "Bing routes",
        aerialTitle: "Bing images aériennes",
        labeledAerialTitle: "Bing images aériennes avec étiquettes"
    },    

    "gxp.plugins.FeatureEditor.prototype": {
        splitButtonText: "Edit",
        createFeatureActionText: "Create",
        editFeatureActionText: "Modify",
        createFeatureActionTip: "Créer un nouvel objet",
        editFeatureActionTip: "Modifier un objet existant"
    },
    
    "gxp.plugins.FeatureGrid.prototype": {
        displayFeatureText: "Afficher sur la carte",
        firstPageTip: "Première page",
        previousPageTip: "Page précédente",
        zoomPageExtentTip: "Zoom sur la page",
        nextPageTip: "Page suivante",
        lastPageTip: "Dernière page",
        totalMsg: "Features {1} to {2} of {0}"
    },

    "gxp.plugins.GoogleEarth.prototype": {
        menuText: "Passer à la visionneuse 3D",
        tooltip: "Passer à la visionneuse 3D"
    },
    
    "gxp.plugins.GoogleSource.prototype": {
        title: "Calques Google",
        roadmapAbstract: "Carte routière",
        satelliteAbstract: "Images satellite",
        hybridAbstract: "Images avec routes",
        terrainAbstract: "Carte routière avec le terrain"
    },

    "gxp.plugins.LayerProperties.prototype": {
        menuText: "Propriétés de la couche",
        toolTip: "Propriétés de la couche"
    },
    
    "gxp.plugins.LayerTree.prototype": {
        shortTitle: "Layers",
        rootNodeText: "Layers",
        overlayNodeText: "Surimpressions",
        baseNodeText: "Couches"
    },

    "gxp.plugins.LayerManager.prototype": {
        baseNodeText: "Couche"
    },

    "gxp.plugins.Legend.prototype": { 
        menuText: "Légende",
        tooltip: "Légende"
    },

    "gxp.plugins.Measure.prototype": {
        buttonText: "Mesure",
        lengthMenuText: "Longueur",
        areaMenuText: "Surface",
        lengthTooltip: "Mesure de longueur",
        areaTooltip: "Mesure de surface",
        measureTooltip: "Mesure"
    },

    "gxp.plugins.Navigation.prototype": {
        menuText: "Panner la carte",
        tooltip: "Panner la carte"
    },

    "gxp.plugins.NavigationHistory.prototype": {
        previousMenuText: "Position précédente",
        nextMenuText: "Position suivante",
        previousTooltip: "Position précédente",
        nextTooltip: "Position suivante"
    },

    "gxp.plugins.LoadingIndicator.prototype": {
        loadingMapMessage: "Chargement de la carte..."
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

    "gxp.plugins.OSMSource.prototype": {
        title: "Calques OpenStreetMap",
        mapnikAttribution: "&copy; <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors",
        osmarenderAttribution: "Données CC-By-SA par <a href='http://openstreetmap.org/'>OpenStreetMap</a>"
    },

    "gxp.plugins.Print.prototype": {
        buttonText:"Imprimer",
        menuText: "Imprimer la carte",
        tooltip: "Imprimer la carte",
        previewText: "Aperçu avant impression",
        notAllNotPrintableText: "Non, toutes les couches peuvent être imprimées",
        nonePrintableText: "Aucune de vos couches ne peut être imprimée"
    },

    "gxp.plugins.MapQuestSource.prototype": {
        title: "MapQuest Layers",
        osmAttribution: "Avec la permission de tuiles <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a> <img src='http://developer.mapquest.com/content/osm/mq_logo.png' border='0'>",
        osmTitle: "MapQuest OpenStreetMap",
        naipAttribution: "Avec la permission de tuiles <a href='http://open.mapquest.co.uk/' target='_blank'>MapQuest</a> <img src='http://developer.mapquest.com/content/osm/mq_logo.png' border='0'>",
        naipTitle: "MapQuest Imagery"
    },

    "gxp.plugins.QueryForm.prototype": {
        queryActionText: "Interrogation",
        queryMenuText: "Couche de requêtes",
        queryActionTip: "Interroger la couche sélectionnée",
        queryByLocationText: "Query by current map extent",
        queryByAttributesText: "Requête par attributs"
    },

    "gxp.plugins.RemoveLayer.prototype": {
        removeMenuText: "Enlever la couche",
        removeActionTip: "Enlever la couche"
    },

    "gxp.plugins.WMSGetFeatureInfo.prototype": {
        buttonText:"Identify",
        infoActionTip: "Get Feature Info",
        popupTitle: "Info sur l'objet"
    },

    "gxp.plugins.Zoom.prototype": {
        zoomMenuText: "Zoom Box",
        zoomInMenuText: "Zoom avant",
        zoomOutMenuText: "Zoom arrière",
        zoomTooltip: "Zoom by dragging a box",
        zoomInTooltip: "Zoom avant",
        zoomOutTooltip: "Zoom arrière"
    },
    
    "gxp.plugins.ZoomToExtent.prototype": {
        menuText: "Zoomer sur la carte max",
        tooltip: "Zoomer sur la carte max"
    },
    
    "gxp.plugins.ZoomToDataExtent.prototype": {
        menuText: "Zoomer sur la couche",
        tooltip: "Zoomer sur la couche"
    },

    "gxp.plugins.ZoomToLayerExtent.prototype": {
        menuText: "Zoomer sur la couche",
        tooltip: "Zoomer sur la couche"
    },
    
    "gxp.plugins.ZoomToSelectedFeatures.prototype": {
        menuText: "Zoomer sur les objets sélectionnés",
        tooltip: "Zoomer sur les objets sélectionnés"
    },

    "gxp.FeatureEditPopup.prototype": {
        closeMsgTitle: "Enregistrer les modifications ?",
        closeMsg: "Cet objet a des modifications non enregistrées. Voulez-vous enregistrer vos modifications ?",
        deleteMsgTitle: "Supprimer l'objet ?",
        deleteMsg: "Etes-vous sûr de vouloir supprimer cet objet ?",
        editButtonText: "Modifier",
        editButtonTooltip: "Modifier cet objet",
        deleteButtonText: "Supprimer",
        deleteButtonTooltip: "Supprimer cet objet",
        cancelButtonText: "Annuler",
        cancelButtonTooltip: "Arrêter de modifier, annuler les modifications",
        saveButtonText: "Enregistrer",
        saveButtonTooltip: "Enregistrer les modifications"
    },
    
    "gxp.FillSymbolizer.prototype": {
        fillText: "Remplir",
        colorText: "Couleur",
        opacityText: "Opacité"
    },
    
    "gxp.FilterBuilder.prototype": {
        builderTypeNames: ["Tout", "tous", "aucun", "pas tout"],
        preComboText: "Match",
        postComboText: "de ce qui suit:",
        addConditionText: "Ajouter la condition",
        addGroupText: "Ajouter un groupe",
        removeConditionText: "Supprimer la condition"
    },
    
    "gxp.grid.CapabilitiesGrid.prototype": {
        nameHeaderText : "Nom",
        titleHeaderText : "Titre",
        queryableHeaderText : "Interrogeable",
        layerSelectionLabel: "Voir les données disponibles à partir de :",
        layerAdditionLabel: "ou ajouter un nouveau serveur.",
        expanderTemplateText: "<p><b>Résumé:</b> {abstract}</p>"
    },
    
    "gxp.PointSymbolizer.prototype": {
        graphicCircleText: "Cercle",
        graphicSquareText: "Carré",
        graphicTriangleText: "Triangle",
        graphicStarText: "Étoile",
        graphicCrossText: "Croix",
        graphicXText: "x",
        graphicExternalText: "Externe",
        urlText: "URL",
        opacityText: "Opacité",
        symbolText: "Symbole",
        sizeText: "Taille",
        rotationText: "Rotation"
    },

    "gxp.QueryPanel.prototype": {
        queryByLocationText: "Interrogation selon le lieu",
        currentTextText: "Mesure actuelle",
        queryByAttributesText: "Requête par attributs",
        layerText: "Calque"
    },
    
    "gxp.RulePanel.prototype": {
        scaleSliderTemplate: "{scaleType} échelle 1:{scale}",
        labelFeaturesText: "Label Caractéristiques",
        advancedText: "Avancé",
        limitByScaleText: "Limiter par l'échelle",
        limitByConditionText: "Limiter par condition",
        symbolText: "Symbole",
        nameText: "Nom"
    },
    
    "gxp.ScaleLimitPanel.prototype": {
        scaleSliderTemplate: "{scaleType} échelle 1:{scale}",
        maxScaleLimitText: "Échelle maximale"
    },
    
    "gxp.TextSymbolizer.prototype": {
        labelValuesText: "Label valeurs",
        haloText: "Halo",
        sizeText: "Taille"
    },
    
    "gxp.WMSLayerPanel.prototype": {
        aboutText: "A propos",
        titleText: "Titre",
        nameText: "Nom",
        descriptionText: "Description",
        displayText: "Affichage",
        opacityText: "Opacité",
        formatText: "Format",
        transparentText: "Transparent",
        cacheText: "Cache",
        cacheFieldText: "Utiliser la version mise en cache",
        infoFormatText: "Info format",
        infoFormatEmptyText: "Choisissez un format"
    },

    "gxp.EmbedMapDialog.prototype": {
        publishMessage: "Votre carte est prête à être publiée sur le web. Il suffit de copier le code HTML suivant pour intégrer la carte dans votre site Web :",
        heightLabel: 'Hauteur',
        widthLabel: 'Largeur',
        mapSizeLabel: 'Taille de la carte',
        miniSizeLabel: 'Mini',
        smallSizeLabel: 'Petit',
        premiumSizeLabel: 'Premium',
        largeSizeLabel: 'Large'
    },

    "gxp.LayerUploadPanel.prototype": {
        titleLabel: "Titre",
        titleEmptyText: "Titre de la couche",
        abstractLabel: "Description",
        abstractEmptyText: "Description couche",
        fileLabel: "Données",
        fieldEmptyText: "Parcourir pour ...",
        uploadText: "Upload",
        waitMsgText: "Transfert de vos données ...",
        invalidFileExtensionText: "L'extension du fichier doit être : ",
        optionsText: "Options",
        workspaceLabel: "Espace de travail",
        workspaceEmptyText: "Espace de travail par défaut",
        dataStoreLabel: "Magasin de données",
        dataStoreEmptyText: "Create new store",
        defaultDataStoreEmptyText: "Magasin de données par défaut"
    },

    "gxp.NewSourceDialog.prototype": {
        title: "Ajouter un nouveau serveur...",
        cancelText: "Annuler",
        addServerText: "Ajouter un serveur",
        invalidURLText: "Indiquez l'URL valide d'un serveur WMS (e.g. http://example.com/geoserver/wms)",
        contactingServerText: "Interrogation du serveur..."
    },

    "gxp.ScaleOverlay.prototype": { 
        zoomLevelText: "Niveau de zoom"
    }

});
/* ======================================================================
    FeatureEditing/resources/lang/fr.js
   ====================================================================== */

OpenLayers.Util.extend(OpenLayers.Lang.fr, {
    'Attributes': 'Attributs',
    'Delete feature': 'Supprimer objet',
    'Delete Feature': 'Supprimer Objet',
    'Do you really want to delete this feature ?': 'Voulez-vous vraiment supprimer cet objet ?',
    'Delete': 'Supprimer',
    'Export KML': 'Exporter KML',
    'Export': 'Exporter',
    'Import KML': 'Importer KML',
    'Import': 'Importer',
    'Edit Feature': 'Editer Objet',
    'LineString': 'Ligne',
    'MultiLineString': 'MultiLigne',
    'Point': 'Point',
    'Circle': 'Cercle',
    'Box': 'Rectangle',
    'MultiPoint': 'MultiPoint',
    'Polygon': 'Polygone',
    'MultiPolygon': 'MultiPolygone',
    'Label': 'Etiquette',
    'Create point': 'Créer point',
    'Create circle': 'Créer cercle',
    'Create box': 'Créer rectangle',
    'Create line': 'Créer ligne',
    'Create polygon': 'Créer polygone',
    'Create label': 'Créer étiquette',
    'Delete all features': 'Supprimer tous les objets',
    'DeleteAll': 'Tous supprimer',
    'Delete All Features': 'Supprimer Tous Les Objets',
    'Do you really want to delete all features ?': 'Voulez-vous vraiment supprimer tous les objets ?',
    'RedLining Panel': 'Outil de dessin',
    'Close': 'Fermer',
    'Style': 'Style',
    'color': 'couleur',
    'select a color...': 'couleur...',

     /* colors for styler */
    'blue': 'bleu',
    'red': 'rouge',
    'green': 'vert',
    'yellow': 'jaune',
    'orange': 'orange',
    'purple': 'violet',
    'white': 'blanc',
    'black': 'noir',
    'gray': 'gris',
    'pink': 'rose',
    'brown': 'brun',
    'cyan': 'cyan',
    'lime': 'lime',
    'indigo': 'indigo',
    'magenta': 'magenta',
    'maroon': 'maron',
    'olive': 'olive',
    'plum': 'prune',
    'salmon': 'saumon',
    'gold': 'or',
    'silver': 'argent'
});
/* ======================================================================
    Proj/Lang/GeoExt-fr.js
   ====================================================================== */

/*
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("fr", {
});
/* ======================================================================
    Styler/lang/fr.js
   ====================================================================== */

/*
 * French translation file
 */
OpenLayers.Lang.fr = OpenLayers.Util.extend(OpenLayers.Lang.fr, {
    /* SpatialComboBox.js */
    "intersects": "intersection avec",
    "inside": "à l'intérieur de",
    "contains": "contient l'objet",
    /* FilterPanel.js */
    "This field is mandatory": "Ce champ est nécessaire",
    /* SpatialFilterPanel.js */
    "Modify geometry": "Modifier la géométrie",
    "Save this geometry": "Enregistrer cette géométrie",
    "spatialfilterpanel.geometry.saved": "Géométrie enregistrée pour 30 jours sur ce navigateur.",
    /* FilterBuilder.js */
    "any": "une de",
    "all": "toutes",
    "none": "aucune de",
    "not all": "pas toutes",
    "Matching": "Correspondre à",
    "these conditions:": "ces conditions :",
    "Spatial condition": "Condition spatiale",
    "Group": "Groupe",
    "based on a point": "basée sur un point",
    "based on a line": "basée sur une ligne",
    "based on a polygon": "basée sur un polygone",
    "based on a stored geometry": "basée sur une géométrie stockée",
    "Delete this condition": "Supprimer cette condition",
    /* Styler.js */
    "Unable to read capabilities from WMS":
        "Impossible de lire les 'capabilities' depuis WMS",
    "Unable to read capabilities from WFS":
        "Impossible de lire les 'capabilities' depuis WFS",
    "Add new": "Créer une nouvelle règle",
    "Delete selected": "Supprimer la selection",
    "styler.delete.rule":
        "Supprimer la règle ${NAME} ?",
    "Delete rule": "Supprimer la règle",
    "Layers": "Couches",
    "styler.feature": "Objet : ${FEATURE}",
    "Rules used to render this feature:":
        "Règles utilisées pour cet objet :",
    "Attributes of this feature:": "Attributs de cet objet :",
    "styler.style": "Style : ${STYLE}",
    "Untitled": "Sans titre",
    "styler.div.zoomlevel": "<div>{zoomType} Niveau de zoom : {zoom}</div>",
    "styler.div.mapzoom": "<div>Zoom actuel de la carte: {mapZoom}</div>",
    "Circle": "Cercle",
    "Square": "Carré",
    "Triangle": "Triangle",
    "Star": "Étoile",
    "Cross": "Croix",
    "X": "X",
    "Custom...": "Personnalisé...",
    "Cancel": "Annuler",
    "Save": "Sauvegarder",
    "Could not load features from the WFS":
        "Impossible de charger les objets par WFS",
    /* ColorManager.js */
    "Color Picker": "Selecteur de couleur",
    /* FillSymbolizer.js */
    "Fill": "Remplissage",
    "Color": "Couleur",
    "Opacity": "Opacité",
    /* LegendPanel.js */
    "Untitled ": "Sans titre ",
    /* PointSymbolizer.js */
    "Circle": "Cercle",
    "Square": "Carré",
    "Triangle": "Triangle",
    "Star": "Étoile",
    "Cross": "Croix",
    "X": "X",
    "External": "Externe",
    "URL": "URL",
    "Opacity": "Opacité",
    "Symbol": "Symbole",
    "Radius": "Taille",
    "Rotation": "Rotation",
    /* RuleBuilder.js */
    "Add rule": "Créer une règle",
    "Untitled ": "Sans titre ",
    /* RuleChooser.js */
    "Styling rules that apply for this feature":
        "Règles de style qui peuvent s'appliquer à cet objet",
    "Default": "Par défaut",
    '{type} for the "{layer}" layer':
        '{type} pour la couche "{layer}"',
    'Create a new styling rule': "Créer une nouvelle règle de style",
    "Other styling rules": "Autres règles de style",
    "Styling rules": "Règles de style",
    /* RulePanel.js */
    "{type} scale 1:{scale}": "Échelle {type} 1:{scale}",
    "Labels": "Libellés",
    "Simple": "Simple",
    "Advanced": "Avancé",
    "Limit by scale": "Limite par échelle",
    "Limit by condition": "Limite par condition",
    "Symbol": "Symbole",
    "Name": "Nom",
    /* ScaleLimitPanel.js */
    "Min scale": "Échelle min",
    "Max scale": "Échelle max",
    /* StrokeSymbolizer.js */
    "Solid": "Plein",
    "Dash": "Tiret",
    "Dot": "Point",
    "Border": "Contour",
    "Style": "Style",
    "Color": "Couleur",
    "Width": "Épaisseur",
    "Opacity": "Opacité",
    /* TextSymbolizer */
    "Attribute": "Attribut",
    "Size: ": "Taille : ",
    "Halo": "Halo",
    "Size": "Taille",
    /* ScaleSliderTip.js */
    "Zoom Level: {zoom}": "Niveau de zoom : {zoom}",
    "Resolution: {resolution}": "Résolution: {resolution}",
    "Scale: 1 : {scale}": "Échelle: 1 : {scale}"
    // no trailing comma
});
/* ======================================================================
    Proj/Lang/fr.js
   ====================================================================== */

OpenLayers.Util.extend(OpenLayers.Lang.fr, {
    "layertree": "Thèmes"
});
/* ======================================================================
    Ext/src/locale/ext-lang-fr.js
   ====================================================================== */

/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
﻿/*
 * France (France) translation
 * By Thylia
 * 09-11-2007, 02:22 PM
 * updated by disizben (22 Sep 2008)
 * updated by Thylia (20 Apr 2010)
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">En cours de chargement...</div>';

if(Ext.DataView){
   Ext.DataView.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
   Ext.grid.GridPanel.prototype.ddText = "{0} ligne{1} sélectionnée{1}";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "En cours de chargement...";
}

Date.shortMonthNames = [
   "Janv",
   "Févr",
   "Mars",
   "Avr",
   "Mai",
   "Juin",
   "Juil",
   "Août",
   "Sept",
   "Oct",
   "Nov",
   "Déc"
];

Date.getShortMonthName = function(month) {
  return Date.shortMonthNames[month];
};

Date.monthNames = [
   "Janvier",
   "Février",
   "Mars",
   "Avril",
   "Mai",
   "Juin",
   "Juillet",
   "Août",
   "Septembre",
   "Octobre",
   "Novembre",
   "Décembre"
];

Date.monthNumbers = {
  "Janvier" : 0,
  "Février" : 1,
  "Mars" : 2,
  "Avril" : 3,
  "Mai" : 4,
  "Juin" : 5,
  "Juillet" : 6,
  "Août" : 7,
  "Septembre" : 8,
  "Octobre" : 9,
  "Novembre" : 10,
  "Décembre" : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[Ext.util.Format.capitalize(name)];
};

Date.dayNames = [
   "Dimanche",
   "Lundi",
   "Mardi",
   "Mercredi",
   "Jeudi",
   "Vendredi",
   "Samedi"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

Date.parseCodes.S.s = "(?:er)";

Ext.override(Date, {
    getSuffix : function() {
        return (this.getDate() == 1) ? "er" : "";
    }
});

if(Ext.MessageBox){
    Ext.MessageBox.buttonText = {
        ok     : "OK",
        cancel : "Annuler",
        yes    : "Oui",
        no     : "Non"
    };
}

if(Ext.util.Format){
    Ext.util.Format.date = function(v, format){
        if(!v) return "";
        if(!Ext.isDate(v)) v = new Date(Date.parse(v));
        return v.dateFormat(format || "d/m/Y");
    };
    Ext.util.Format.plural = function(v, s, p) {
        return v + ' ' + (v <= 1 ? s : (p ? p : s + 's'));
    };
}

if(Ext.DatePicker){
    Ext.apply(Ext.DatePicker.prototype, {
        todayText         : "Aujourd'hui",
        minText           : "Cette date est antérieure à la date minimum",
        maxText           : "Cette date est postérieure à la date maximum",
        disabledDaysText  : "",
        disabledDatesText : "",
        monthNames        : Date.monthNames,
        dayNames          : Date.dayNames,
        nextText          : 'Mois suivant (CTRL+Flèche droite)',
        prevText          : "Mois précédent (CTRL+Flèche gauche)",
        monthYearText     : "Choisissez un mois (CTRL+Flèche haut ou bas pour changer d'année.)",
        todayTip          : "{0} (Barre d'espace)",
        okText            : "&#160;OK&#160;",
        cancelText        : "Annuler",
        format            : "d/m/y",
        startDay          : 1
    });
}

if(Ext.PagingToolbar){
    Ext.apply(Ext.PagingToolbar.prototype, {
        beforePageText : "Page",
        afterPageText  : "sur {0}",
        firstText      : "Première page",
        prevText       : "Page précédente",
        nextText       : "Page suivante",
        lastText       : "Dernière page",
        refreshText    : "Actualiser la page",
        displayMsg     : "Page courante {0} - {1} sur {2}",
        emptyMsg       : 'Aucune donnée à afficher'
    });
}

if(Ext.form.BasicForm){
    Ext.form.BasicForm.prototype.waitTitle = "Veuillez patienter...";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "La valeur de ce champ est invalide";
}

if(Ext.form.TextField){
    Ext.apply(Ext.form.TextField.prototype, {
        minLengthText : "La longueur minimum de ce champ est de {0} caractère(s)",
        maxLengthText : "La longueur maximum de ce champ est de {0} caractère(s)",
        blankText     : "Ce champ est obligatoire",
        regexText     : "",
        emptyText     : null
    });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      decimalSeparator : ",",
      decimalPrecision : 2,
      minText : "La valeur minimum de ce champ doit être de {0}",
      maxText : "La valeur maximum de ce champ doit être de {0}",
      nanText : "{0} n'est pas un nombre valide"
   });
}

if(Ext.form.DateField){
   Ext.apply(Ext.form.DateField.prototype, {
      disabledDaysText  : "Désactivé",
      disabledDatesText : "Désactivé",
      minText           : "La date de ce champ ne peut être antérieure au {0}",
      maxText           : "La date de ce champ ne peut être postérieure au {0}",
      invalidText       : "{0} n'est pas une date valide - elle doit être au format suivant: {1}",
      format            : "d/m/y",
      altFormats        : "d/m/Y|d-m-y|d-m-Y|d/m|d-m|dm|dmy|dmY|d|Y-m-d",
      startDay          : 1
   });
}

if(Ext.form.ComboBox){
   Ext.apply(Ext.form.ComboBox.prototype, {
      loadingText       : "En cours de chargement...",
      valueNotFoundText : undefined
   });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Ce champ doit contenir une adresse email au format: "usager@example.com"',
      urlText      : 'Ce champ doit contenir une URL au format suivant: "http:/'+'/www.example.com"',
      alphaText    : 'Ce champ ne peut contenir que des lettres et le caractère souligné (_)',
      alphanumText : 'Ce champ ne peut contenir que des caractères alphanumériques ainsi que le caractère souligné (_)'
   });
}

if(Ext.form.HtmlEditor){
   Ext.apply(Ext.form.HtmlEditor.prototype, {
      createLinkText : "Veuillez entrer l'URL pour ce lien:",
          buttonTips : {
              bold : {
                  title: 'Gras (Ctrl+B)',
                  text: 'Met le texte sélectionné en gras.',
                  cls: 'x-html-editor-tip'
              },
              italic : {
                  title: 'Italique (Ctrl+I)',
                  text: 'Met le texte sélectionné en italique.',
                  cls: 'x-html-editor-tip'
              },
              underline : {
                  title: 'Souligné (Ctrl+U)',
                  text: 'Souligne le texte sélectionné.',
                  cls: 'x-html-editor-tip'
              },
              increasefontsize : {
                  title: 'Agrandir la police',
                  text: 'Augmente la taille de la police.',
                  cls: 'x-html-editor-tip'
              },
              decreasefontsize : {
                  title: 'Réduire la police',
                  text: 'Réduit la taille de la police.',
                  cls: 'x-html-editor-tip'
              },
              backcolor : {
                  title: 'Couleur de surbrillance',
                  text: 'Modifie la couleur de fond du texte sélectionné.',
                  cls: 'x-html-editor-tip'
              },
              forecolor : {
                  title: 'Couleur de police',
                  text: 'Modifie la couleur du texte sélectionné.',
                  cls: 'x-html-editor-tip'
              },
              justifyleft : {
                  title: 'Aligner à gauche',
                  text: 'Aligne le texte à gauche.',
                  cls: 'x-html-editor-tip'
              },
              justifycenter : {
                  title: 'Centrer',
                  text: 'Centre le texte.',
                  cls: 'x-html-editor-tip'
              },
              justifyright : {
                  title: 'Aligner à droite',
                  text: 'Aligner le texte à droite.',
                  cls: 'x-html-editor-tip'
              },
              insertunorderedlist : {
                  title: 'Liste à puce',
                  text: 'Démarre une liste à puce.',
                  cls: 'x-html-editor-tip'
              },
              insertorderedlist : {
                  title: 'Liste numérotée',
                  text: 'Démarre une liste numérotée.',
                  cls: 'x-html-editor-tip'
              },
              createlink : {
                  title: 'Lien hypertexte',
                  text: 'Transforme en lien hypertexte.',
                  cls: 'x-html-editor-tip'
              },
              sourceedit : {
                  title: 'Code source',
                  text: 'Basculer en mode édition du code source.',
                  cls: 'x-html-editor-tip'
              }
        }
   });
}

if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Tri croissant",
      sortDescText : "Tri décroissant",
      columnsText  : "Colonnes"
   });
}

if(Ext.grid.GroupingView){
   Ext.apply(Ext.grid.GroupingView.prototype, {
      emptyGroupText : '(Aucun)',
      groupByText    : 'Grouper par ce champ',
      showGroupsText : 'Afficher par groupes'
   });
}

if(Ext.grid.PropertyColumnModel){
    Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
        nameText   : "Propriété",
        valueText  : "Valeur",
        dateFormat : "d/m/Y",
        trueText   : "vrai",
        falseText  : "faux"
    });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
   Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
      splitTip            : "Cliquer et glisser pour redimensionner le panneau.",
      collapsibleSplitTip : "Cliquer et glisser pour redimensionner le panneau. Double-cliquer pour le cacher."
   });
}

if(Ext.form.TimeField){
   Ext.apply(Ext.form.TimeField.prototype, {
      minText     : "L'heure de ce champ ne peut être antérieure à {0}",
      maxText     : "L'heure de ce champ ne peut être postérieure à {0}",
      invalidText : "{0} n'est pas une heure valide",
      format      : "H:i",
      altFormats  : "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|h a|g a|g A|gi|hi|Hi|gia|hia|g|H"
   });
}

if(Ext.form.CheckboxGroup){
  Ext.apply(Ext.form.CheckboxGroup.prototype, {
    blankText : "Vous devez sélectionner au moins un élément dans ce groupe"
  });
}

if(Ext.form.RadioGroup){
  Ext.apply(Ext.form.RadioGroup.prototype, {
    blankText : "Vous devez sélectionner au moins un élément dans ce groupe"
  });
}
/* ======================================================================
    GeoExt/locale/GeoExt-fr.js
   ====================================================================== */

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/Lang.js
 */

GeoExt.Lang.add("fr", {
    "GeoExt.tree.LayerContainer.prototype": {
        text: "Couches"
    },
    "GeoExt.tree.BaseLayerContainer.prototype": {
        text: "Couches de base"
    },
    "GeoExt.tree.OverlayLayerContainer.prototype": {
        text: "Couches additionnelles"
    }
});
