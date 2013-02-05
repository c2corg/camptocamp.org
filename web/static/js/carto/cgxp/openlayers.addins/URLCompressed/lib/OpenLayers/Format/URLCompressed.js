/* Copyright (c) 2012. Published under the Clear BSD license.
 * See http://svn.openlayers.org/trunk/openlayers/license.txt for the
 * full text of the license. */

/**
 * @requires OpenLayers/Format.js
 * @include OpenLayers/Feature/Vector.js
 * @include OpenLayers/Geometry/Point.js
 * @include OpenLayers/Geometry/MultiPoint.js
 * @include OpenLayers/Geometry/LineString.js
 * @include OpenLayers/Geometry/MultiLineString.js
 * @include OpenLayers/Geometry/Polygon.js
 * @include OpenLayers/Geometry/MultiPolygon.js
 */

/**
 * Class: OpenLayers.Format.URLCompressed
 * Read and write URLCompressed. Create a new parser with the
 *     <OpenLayers.Format.URLCompressed> constructor.
 *
 * Point encoding was inspired by this blog entry:
 * http://soulsolutions.com.au/Default.aspx?tabid=96
 *
 * Global syntax: Fp(geom~attributes~style)
 *   F mean Feature collection
 *   p mean point possible values:
 *      plaPLY => Point, LineString, Polygon, Multi...
 *   geom: encoded geometry
 *   attributes and style: key*value list separated by '
 */
OpenLayers.Format.URLCompressed = OpenLayers.Class(OpenLayers.Format, {

    /** api: config[accuracy]
     *  ``Number``
     *  Encoding accuracy, also needed with the same value for decoding.
     *  Default to 1.
     */
    accuracy: 1,

    /** api: config[simplify]
     *  ``Number``
     *  Simplify the geometries before encoding, 0 mean disable (default).
     *  In external projection unit.
     */
    simplify: 0,

    /** api: config[attributes]
     *  ``Object(Array(String))``
     *  List of feature attributes to export.
     *  See `styleAttributes`.
     */
    attributes: { point: null, line: null, polygon: null },

    /** api: config[styleAttributes]
     *  ``Object``
     *  List of feature style attribute to export.
     *  Default to ``null``, mean all.
     *  Example:
     *  ```
     *  {
     *      point: {
     *          'pointRadius': parseFloat, // read function
     *          'fontColor': true // string
     *      ],
     *      line: [
     *          'strokeColor': true,
     *          'strokeWidth': parseFloat
     *      ],
     *      polygon: null // all
     *  }
     *  ```
     *  Default to: `{ point: null, line: null, polygon: null }`
     */
    styleAttributes: { point: null, line: null, polygon: null },

    /**
     * Characters used to encode the data.
     *
     * ~'() will be used as separator, no character still be available.
     */
    _CHAR64: ".-_!*ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghjkmnpqrstuvwxyz",

    /**
     * Encoder and decoder state
     */
    _plat: 0,
    _plon: 0,

    /**
     * Constructor: OpenLayers.Format.URLCompressed
     * Create a new parser for URLCompressed.
     *
     * Parameters:
     * options - {Object} An optional object whose properties will be set on
     *     this instance.
     */

    // Encode a signed number in the encode format.
    encodeSignedNumber: function(num) {
      var sgn_num = num << 1;

      if (num < 0) {
        sgn_num = ~(sgn_num);
      }

      return(this.encodeNumber(sgn_num));
    },

    // Encode an unsigned number in the encode format.
    encodeNumber: function(num) {
      var encodeString = "";

      while (num >= 0x20) {
        encodeString += this._CHAR64[0x20 | (num & 0x1f)];
        num >>= 5;
      }

      encodeString += this._CHAR64[num];
      return encodeString;
    },

    // Create the encoded bounds.
    encodePoints: function(points) {
        var encodedPoints = "";

        for (var i = 0; i < points.length; ++i) {
            var point = points[i];

            var lon = point.x;
            var lat = point.y;

            var lone5 = Math.floor(lon / this.accuracy);
            var late5 = Math.floor(lat / this.accuracy);

            dlon = lone5 - this._plon;
            dlat = late5 - this._plat;

            this._plon = lone5;
            this._plat = late5;

            encodedPoints += this.encodeSignedNumber(dlon) + this.encodeSignedNumber(dlat);
        }
        return encodedPoints;
    },


    // Decode an encoded string into a list of VE lat/lon.
    decodePoints: function(encoded) {
        var len = encoded.length;
        var index = 0;
        var array = [];
        while (index < len) {
            var b;
            var shift = 0;
            var result = 0;
            do {
                b = this._CHAR64.indexOf(encoded.charAt(index++));
                result |= (b & 0x1f) << shift;
                shift += 5;
            } while (b >= 32);
            var dlon = ((result & 1) ? ~(result >> 1) : (result >> 1));
            this._plon += dlon;

            shift = 0;
            result = 0;
            do {
                  b = this._CHAR64.indexOf(encoded.charAt(index++));
                  result |= (b & 0x1f) << shift;
                  shift += 5;
            } while (b >= 32);
            var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
            this._plat += dlat;

            var p = new OpenLayers.Geometry.Point(
                    this._plon * this.accuracy,
                    this._plat * this.accuracy);
            if (this.internalProjection && this.externalProjection) {
                p = p.transform(this.externalProjection,
                        this.internalProjection);
            }
            array.push(p);
        }
        return array;
    },

    /**
     * Method: read
     * Read data from a string, and return an object whose type depends on the
     * subclass.
     *
     * Parameters:
     * data - {string} Data to read/parse.
     *
     * Returns:
     * Depends on the subclass
     */
    read: function(data) {
        this._plat = 0;
        this._plon = 0;
        var results = [];

        if (data.charAt(0) == 'F') {
            data = data.substring(1);
            while (data.length > 0) {
                if (data.charAt(0) == 'A') {
                    var index1 = data.search(/\)~/);
                    var index2 = data.search(/\)\)/);
                    if (index1 < 0 || index2 < index1) {
                        results.push(this.parseFeature(data.substring(0, index2 + 2)));
                        data = data.substring(index2 + 2);
                        break;
                    }
                }
                else {
                    var index = data.search(/\)/);
                    if (index < 0) {
                        break;
                    }
                    results.push(this.parseFeature(data.substring(0, index + 1)));
                    data = data.substring(index + 1);
                }
            }
        }
        else {
            try {
                results = this.parseFeature(data);
                results.type = "Feature";
            } catch (err) {
                if (OpenLayers.Console) {
                    OpenLayers.Console.error(err);
                }
            }
        }
        return results;
    },

    /**
     * Method: parseFeature
     * Convert a feature object from URLCompressed into an
     *     <OpenLayers.Feature.Vector>.
     *
     * Parameters:
     * data - {String} A string created from a URLCompressed object
     *
     * Returns:
     * {<OpenLayers.Feature.Vector>} A feature.
     */
    parseFeature: function(data) {
        var feature, geometry;
        var attributes = {};
        var style = {};
        var g = data.charAt(0);
        var parts = data.substring(2, data.length - 1).split('~');

        var geomType = 'line';
        if (g == 'p' || g == 'P') {
            geomType = 'point';
        }
        else if (g == 'a' || g == 'A') {
            geomType = 'polygon';
        }

        if (parts.length > 1) {
            var attributesdata = parts[1].split("'");
            for (var i = 0, leni = attributesdata.length; i < leni; ++i) {
                var kv = decodeURIComponent(attributesdata[i]).split('*');
                if (this.attributes[geomType]) {
                    var attrib = this.attributes[geomType][kv[0]];
                    if (attrib instanceof Function) {
                        attributes[kv[0]] = attrib(kv[1]);
                    }
                    else {
                        attributes[kv[0]] = kv[1];
                    }
                }
                else {
                    attributes[kv[0]] = kv[1];
                }
            }
        }
        if (parts.length > 2) {
            var styledata = parts[2].split("'");
            for (var j = 0, lenj = styledata.length; j < lenj; ++j) {
                var kvs = decodeURIComponent(styledata[j]).split('*');
                if (this.attributes[geomType]) {
                    var sattrib = this.styleAttributes[geomType][kvs[0]];
                    if (sattrib instanceof Function) {
                        style[kvs[0]] = sattrib(kvs[1]);
                    }
                    else {
                        style[kvs[0]] = kvs[1];
                    }
                }
                else {
                    style[kvs[0]] = kvs[1];
                }
            }
        }

        try {
            geometry = this.parseGeometry(g, parts[0]);
        } catch(err) {
            // deal with bad geometries
            throw err;
        }
        feature = new OpenLayers.Feature.Vector(geometry, attributes, style);
        return feature;
    },

    /**
     * Method: parseGeometry
     * Convert a geometry object from URLCompressed into an <OpenLayers.Geometry>.
     *
     * Parameters:
     * data - {String} A string created from a URLCompressed object
     *
     * Returns:
     * {<OpenLayers.Geometry>} A geometry.
     */
    parseGeometry: function(geom, innerData) {
        switch (geom) {
            case 'p':
                return this.decodePoints(innerData)[0];
            case 'l':
                return new OpenLayers.Geometry.LineString(this.decodePoints(innerData));
            case 'a':
                var rings = [];
                var ringsData = innerData.split("'");
                for (var i = 0, leni = ringsData.length; i < leni; ++i) {
                    rings.push(new OpenLayers.Geometry.LinearRing(
                            this.decodePoints(ringsData[i])));
                }
                return new OpenLayers.Geometry.Polygon(rings);
            case 'P':
                return new OpenLayers.Geometry.MultiPoint(this.decodePoints(innerData));
            case 'L':
                var lines = [];
                var linesData = innerData.split("'");
                for (var j = 0, lenj = linesData.length; j < lenj; ++j) {
                    lines.push(new OpenLayers.Geometry.LineString(
                            this.decodePoints(linesData[j])));
                }
                return new OpenLayers.Geometry.MultiLineString(lines);
            case 'A':
                var polygons = [];
                var polygonsData = innerData.substr(1, innerData.length - 2)
                        .split(")(");
                for (var k = 0, lenk = polygonsData.length; k < lenk; ++k) {
                    var ringss = [];
                    var ringssData = polygonsData[k].split("'");
                    for (var l = 0, lenl = ringssData.length; l < lenl; ++l) {
                        ringss.push(new OpenLayers.Geometry.LinearRing(
                                this.decodePoints(ringssData[l])));
                    }
                    polygons.push(new OpenLayers.Geometry.Polygon(ringss));
                }
                return new OpenLayers.Geometry.MultiPolygon(polygons);
        }
        return null;
    },

    /**
     * APIMethod: write
     * Serialize a feature, geometry, array of features into a URLCompressed string.
     *
     * Parameters:
     * obj - {Object} An <OpenLayers.Feature.Vector>, <OpenLayers.Geometry>,
     *     or an array of features.
     *
     * Returns:
     * {String} The URLCompressed string representation of the input geometry,
     *     features, or array of features.
     */
    write: function(obj) {
        this._plat = 0;
        this._plon = 0;

        var URLCompressed = '';
        if (OpenLayers.Util.isArray(obj)) {
            URLCompressed += "F";
            var numFeatures = obj.length;
            for (var i = 0; i < numFeatures; ++i) {
                var element = obj[i];
                if (element instanceof OpenLayers.Feature.Vector) {
                    // don't export sketch features
                    if (!element._sketch) {
                        URLCompressed += this.extract.feature.apply(
                            this, [element]);
                    }
                }
                else {
                    URLCompressed += this.extract.geometry.apply(
                        this, [element]);
                }
            }
        }
        else if (obj.CLASS_NAME.indexOf("OpenLayers.Geometry") === 0) {
            URLCompressed += this.extract.geometry.apply(this, [obj]);
        }
        else if (obj instanceof OpenLayers.Feature.Vector) {
            URLCompressed += this.extract.feature.apply(this, [obj]);
            if (obj.layer && obj.layer.projection) {
                URLCompressed.crs = this.createCRSObject(obj);
            }
        }
        return URLCompressed;
    },

    extractAttributes: function(attributes, descriptor) {
        var result = "";
        for (var key in attributes) {
            if (attributes.hasOwnProperty(key) &&
                    (!descriptor || descriptor[key])) {
                var value = attributes[key];
                if (result.length !== 0) {
                    result += "'";
                }
                result += encodeURIComponent(
                        key.replace(/[()'*]/g, '_') + "*" +
                        (value).toString().replace(/[()'*]/g, '_'));
            }
        }
        return result;
    },

    doSimplify: function(geometry) {
        if (geometry.simplify) {
            geometry = geometry.clone().simplify(this.simplify);
        }
        else if (geometry instanceof OpenLayers.Geometry.Collection) {
            for (var i = 0, len = geometry.components.length ; i < len ; i++) {
                geometry.components[i] = this.doSimplify(geometry.components[i]);
            }
        }
        return geometry;
    },

    /**
     * Property: extract
     * Object with properties corresponding to the URLCompressed types.
     *     Property values are functions that do the actual value extraction.
     */
    extract: {
        /**
         * Method: extract.feature
         * Return a partial URLCompressed object representing a single feature.
         *
         * Parameters:
         * feature - {<OpenLayers.Feature.Vector>}
         *
         * Returns:
         * {Object} An object representing the point.
         */
        'feature': function(feature) {
            function isEmpty(map) {
                for(var key in map) {
                    if (map.hasOwnProperty(key)) {
                        return false;
                    }
                }
                return true;
            }

            var geom = this.extract.geometry.apply(this, [feature.geometry]);
            var geomType = 'line';
            if (feature.geometry.CLASS_NAME == 'OpenLayers.Geometry.Point' ||
                feature.geometry.CLASS_NAME == 'OpenLayers.Geometry.MultiPoint') {
                geomType = 'point';
            }
            else if (feature.geometry.CLASS_NAME == 'OpenLayers.Geometry.Polygon' ||
                feature.geometry.CLASS_NAME == 'OpenLayers.Geometry.MultiPolygon') {
                geomType = 'polygon';
            }


            if (feature.style && !isEmpty(feature.style)) {
                return geom.substring(0, geom.length - 1) + '~' +
                    this.extractAttributes(feature.attributes,
                        this.attributes[geomType]) + '~' +
                    this.extractAttributes(feature.style,
                        this.styleAttributes[geomType]) + ")";
            }
            if (feature.attributes && !isEmpty(feature.attributes)) {
                return geom.substring(0, geom.length - 1) + '~' +
                    this.extractAttributes(feature.attributes,
                        this.attributes[geomType]) + ")";
            }
            else {
                return geom;
            }
        },

        /**
         * Method: extract.geometry
         * Return a URLCompressed object representing a single geometry.
         *
         * Parameters:
         * geometry - {<OpenLayers.Geometry>}
         *
         * Returns:
         * {Object} An object representing the geometry.
         */
        'geometry': function(geometry) {
            if (geometry === null) {
                return "";
            }
            if (this.internalProjection && this.externalProjection) {
                geometry = geometry.clone();
                geometry.transform(this.internalProjection,
                                   this.externalProjection);
            }
            if (this.simplify > 0) {
                geometry = this.doSimplify(geometry);
            }
            var geometryType = geometry.CLASS_NAME.split('.')[2];
            return this.extract[geometryType.toLowerCase()].apply(this, [geometry]);
        },

        /**
         * Method: extract.point
         * Return an array of coordinates from a point.
         *
         * Parameters:
         * point - {<OpenLayers.Geometry.Point>}
         *
         * Returns:
         * {Array} An array of coordinates representing the point.
         */
        'point': function(point) {
            return "p(" + this.encodePoints([point]) + ")";
        },

        /**
         * Method: extract.multipoint
         * Return an array of point coordinates from a multipoint.
         *
         * Parameters:
         * multipoint - {<OpenLayers.Geometry.MultiPoint>}
         *
         * Returns:
         * {Array} An array of point coordinate arrays representing
         *     the multipoint.
         */
        'multipoint': function(multipoint) {
            return "P(" + this.encodePoints(multipoint.components) + ")";
        },

        /**
         * Method: extract.linestring
         * Return an array of coordinate arrays from a linestring.
         *
         * Parameters:
         * linestring - {<OpenLayers.Geometry.LineString>}
         *
         * Returns:
         * {Array} An array of coordinate arrays representing
         *     the linestring.
         */
        'linestring': function(linestring) {
            return "l(" + this.encodePoints(linestring.components) + ")";
        },

        /**
         * Method: extract.multilinestring
         * Return an array of linestring arrays from a linestring.
         *
         * Parameters:
         * linestring - {<OpenLayers.Geometry.MultiLineString>}
         *
         * Returns:
         * {Array} An array of linestring arrays representing
         *     the multilinestring.
         */
        'multilinestring': function(multilinestring) {
            var result = "";
            for (var i = 0, len = multilinestring.components.length; i < len; i++) {
                var linestring = multilinestring.components[i];
                if (result.length !== 0) {
                    result += "'";
                }
                result += this.encodePoints(linestring.components);
            }
            return "L(" + result + ")";
        },

        /**
         * Method: extract.polygon
         * Return an array of linear ring arrays from a polygon.
         *
         * Parameters:
         * polygon - {<OpenLayers.Geometry.Polygon>}
         *
         * Returns:
         * {Array} An array of linear ring arrays representing the polygon.
         */
        'polygon': function(polygon) {
            var result = "";
            for (var i = 0, len = polygon.components.length; i < len; i++) {
                var curve = polygon.components[i];
                if (result.length !== 0) {
                    result += "'";
                }
                curve = curve.clone();
                // remove duplicate point
                curve.components.pop();
                result += this.encodePoints(curve.components);
            }
            return "a(" + result + ")";
        },

        /**
         * Method: extract.multipolygon
         * Return an array of polygon arrays from a multipolygon.
         *
         * Parameters:
         * multipolygon - {<OpenLayers.Geometry.MultiPolygon>}
         *
         * Returns:
         * {Array} An array of polygon arrays representing
         *     the multipolygon
         */
        'multipolygon': function(multipolygon) {
            var result = "";
            for (var i = 0, leni = multipolygon.components.length; i < leni; i++) {
                var polygon = multipolygon.components[i];
                result += '(';
                var first = true;
                for (var j = 0, lenj = polygon.components.length; j < lenj; j++) {
                    var curve = polygon.components[j];
                    if (first) {
                        first = false;
                    }
                    else {
                        result += "'";
                    }

                    result += this.encodePoints(curve.components);
                }
                result += ')';
            }
            return "A(" + result + ")";
        },

        /**
         * Method: extract.collection
         * Return an array of geometries from a geometry collection.
         *
         * Parameters:
         * collection - {<OpenLayers.Geometry.Collection>}
         *
         * Returns:
         * {Array} An array of geometry objects representing the geometry
         *     collection.
         */
        'collection': function(collection) {
            var result = "";
            for (var i = 0, len = multipolygon.components.length; i < len; i++) {
                result += this.extract.geometry.apply(this, [feature.geometry]);
            }
            return result;
        }
    },

    CLASS_NAME: "OpenLayers.Format.URLCompressed"

});
