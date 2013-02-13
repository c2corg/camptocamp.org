Ext.namespace("c2corg");

c2corg.i18n = function(string) {
    if (typeof c2corg_map_translations == "object" &&
        typeof c2corg_map_translations[string] != "undefined") {
        return c2corg_map_translations[string];
    }
    return string;
};
