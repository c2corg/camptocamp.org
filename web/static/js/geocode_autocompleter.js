// geonames Array autocompleter based on scriptaculous Autocompleter.Base
//
// The constructor takes following parameters:
// - id of the monitored textbox
// - id of the autocompletion menu
// - options block // TODO
Autocompleter.Geocode = Class.create(Autocompleter.Base, {
  initialize: function(element, update, options) {
    this.baseInitialize(element, update, options);
    this.gindex = element;
    // if the element has class geonames, we use this service, else we use nominatim (http://wiki.openstreetmap.org/wiki/Nominatim)
    if ($(element).hasClassName('geonames')) {
      this.service = 'geonames';
    } else {
      this.service = 'nominatim';
    }
  },

  getUpdatedChoices: function() {
    this.startIndicator(); // TODO

    var request = '';
    if (this.service === 'geonames') {
      request = 'http://ws.geonames.org/searchJSON?maxRows=5&callback=c2c_geo.' + this.gindex + 
                '.handleJSON&lang=' + document.documentElement.lang + '&name_startsWith=' +
                encodeURIComponent(this.getToken());
    } else {
      request = 'http://nominatim.openstreetmap.org/search?format=json&limit=5&json_callback=c2c_geo.' + 
                this.gindex + '.handleJSON&email=dev@campto' + 'camp.org&q=' + encodeURIComponent(this.getToken());
    }

    this.getJSON(request);

  },

  handleJSON: function(json) {
    if (this.service === 'geonames') { // geonames
      if (json.totalResultsCount > 0) {
        var ul = '<ul>';
        var place;
        for (place in json.geonames) {
          if (json.geonames.hasOwnProperty(place)) {
            ul += '<li data-lat="' + json.geonames[place].lat + '" data-lon="' + json.geonames[place].lng +
                  '">' + json.geonames[place].name + ' <br /><em>[' + json.geonames[place].fcodeName +
                  ' - ' + json.geonames[place].countryName + ']</em></li>';
          }
        }
        this.updateChoices(ul + '</ul>');
      } else {
        this.updateChoices('<ul><li>error</li></ul>'); // TODO
      }

    } else { // nominatim
      if (json.size() > 0) {
        var ul = '<ul>';
        var place;
        for (place in json) {
          if (json.hasOwnProperty(place)) {
            ul += '<li data-lat="' + json[place].lat + '" data-lon="' + json[place].lon + '">' +
                  json[place].display_name + '</li>';
          }
        }
        this.updateChoices(ul + '</ul>');
      } else {
        this.updateChoices('<ul><li>error</li></ul>'); // TODO
      }
    }
  },

  // asynchrously load script to call the geonames.org JSON webservice
  getJSON: function(url) {
    var a = document.createElement('script'), h = document.getElementsByTagName('head')[0];
    a.async = 1;
    a.src = url;
    h.appendChild(a);
  }
});

var c2c_geo = {};

c2c_geo.update_on_select_change = function(elt) {
    var index = $(elt + '_sel').options.selectedIndex;

    // reset fields and hide all inner spans
    $(elt + '_lat').value = '';
    $(elt + '_lon').value = '';
    $(elt + '_geocode', elt + '_geolocation_not_supported',
      elt + '_geolocation_waiting', elt + '_geolocation_ok',
      elt + '_geolocation_failed').invoke('hide');
    // TODO other cases

    if (index === 0)
    {
        $(elt + '_span').hide();
    }
    else
    {
      // display high level span
      $(elt + '_span').show();

      // display only relevant inner span
      if (index === 1) { // geocode autocompleter
        $(elt + '_geocode').show();
      } else if (index === 2) { // user geolocalization
        if (navigator.geolocation) {
          $(elt + '_geolocation_waiting').show();
          navigator.geolocation.getCurrentPosition(
            function(position) {
              $(elt + '_geolocation_waiting').hide();
              $(elt + '_geolocation_ok').show();
              $(elt + '_lat').value = position.coords.latitude;
              $(elt + '_lon').value = position.coords.longitude;
            },
            function(msg) {
              $(elt + '_geolocation_waiting').hide();
              $(elt + '_geolocation_failed').show();
            });
        } else {
          // geolocation not supported by browser
          $(elt + '_geolocation_not_supported').show();
        }
      }

      // TODO other cases: coordinates (with map to make it easy) & user profile localization??
    }

};

(function() { 
  $$('.geocode_auto_complete').each(function(obj) {
    var name = obj.id;
    c2c_geo[name] = new Autocompleter.Geocode(name, name + '_auto_complete', {
                      minChars: 3,
                      afterUpdateElement: function(inputField, selectedItem) {
                        $(name + '_lat').value = selectedItem.getAttribute('data-lat');
                        $(name + '_lon').value = selectedItem.getAttribute('data-lon');
                      }
                    });
  });
})();
