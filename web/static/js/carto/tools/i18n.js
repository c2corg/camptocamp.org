Ext.namespace("c2corg");

c2corg.i18n = function (string, context) {
    if (typeof c2corg_map_translations == "object" &&
        typeof c2corg_map_translations[string] != "undefined") {
        string = c2corg_map_translations[string];
        if (context) {
            string = OpenLayers.String.format(string, context);
        }
    }
    return string;
};
