// geonames Array autocompleter based on scriptaculous Autocompleter.Base
//
// The constructor takes following parameters:
// - id of the monitored textbox
// - id of the autocompletion menu
// - callback prefix
// - options block
//
Autocompleter.Geocode = Class.create(Autocompleter.Base, {
  initialize: function(element, update, serviceCallbackPrefix, options) {
    this.baseInitialize(element, update, options);
    this.serviceCallbackPrefix = serviceCallbackPrefix || '';
    this.options.keepFocus = (typeof this.options.keepFocus == 'undefined') ? true : this.options.keepFocus;
    // if the element has class geonames, we use this service, else we use nominatim (http://wiki.openstreetmap.org/wiki/Nominatim)
    if ($(element).hasClassName('geonames')) {
      this.service = 'geonames';
    } else {
      this.service = 'nominatim';
    }
    // the 'no result' translated string is found as a dataset of the input element
    this.noresult = $(element).getAttribute('data-noresult');
  },

  getUpdatedChoices: function() {
    this.startIndicator();

    var request = '';
    if (this.service === 'geonames') {
      request = 'http://api.geonames.org/searchJSON?maxRows=10&featureClass=P&featureClass=T' +
                '&username=c2corg&callback=' + this.serviceCallbackPrefix + 
                '.handleJSON&lang=' + document.documentElement.lang + '&name_startsWith=' +
                encodeURIComponent(this.getToken());
    } else {
      request = 'http://nominatim.openstreetmap.org/search?format=json&limit=10&json_callback=' + 
                this.serviceCallbackPrefix + '.handleJSON&email=dev@campto' + 'camp.org&q=' +
                encodeURIComponent(this.getToken());
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
                  '">' + json.geonames[place].name + '<br /><em class="informal">[' + json.geonames[place].adminName1 +
                  ' - ' + json.geonames[place].countryName + ']</em></li>';
          }
        }
        this.updateChoices(ul + '</ul>');
      } else {
        this.updateChoices('<ul><div class="feedback">' + this.noresult + '</div></ul>');
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
        this.updateChoices('<ul><div class="feedback">' + this.noresult + '</div></ul>');
      }
    }
  },

  // asynchrously load script to call the geonames.org JSON webservice
  getJSON: function(url) {
    var a = document.createElement('script'), h = document.getElementsByTagName('head')[0];
    a.async = 1;
    a.src = url;
    h.appendChild(a);
  },

  // Base.Autocompleter puts back focus to the input element after element selection
  // which we don't want in mobile version
  // so the following function is basic copy/paste with juste the possibility to
  // disable focus() with options
  updateElement: function(selectedElement) {
    if (this.options.updateElement) {
      this.options.updateElement(selectedElement);
      return;
    }
    var value = '';
    if (this.options.select) {
      var nodes = $(selectedElement).select('.' + this.options.select) || [];
      if(nodes.length>0) value = Element.collectTextNodes(nodes[0], this.options.select);
    } else
      value = Element.collectTextNodesIgnoreClass(selectedElement, 'informal');

    var bounds = this.getTokenBounds();
    if (bounds[0] != -1) {
      var newValue = this.element.value.substr(0, bounds[0]);
      var whitespace = this.element.value.substr(bounds[0]).match(/^\s+/);
      if (whitespace)
        newValue += whitespace[0];
      this.element.value = newValue + value + this.element.value.substr(bounds[1]);
    } else {
      this.element.value = value;
    }
    this.oldElementValue = this.element.value;

    if (this.options.keepFocus) {
      this.element.focus();
    }

    if (this.options.afterUpdateElement)
      this.options.afterUpdateElement(this.element, selectedElement);
  }
});

(function() {

"use strict";

window.C2C = window.C2C || {};

C2C.geo = C2C.geo || {};

C2C.geo.update_around_on_select_change = function(elt) {
  var index = $(elt + '_sel').options.selectedIndex;

  // reset fields and hide all inner spans
  $(elt + '_lat').value = '';
  $(elt + '_lon').value = '';
  $(elt + '_range_span').show();
  $(elt + '_geocode',
    elt + '_geolocation_not_supported',
    elt + '_geolocation_waiting',
    elt + '_geolocation_failed').invoke('hide');

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
      $(elt + '_range_span').hide();
      // detect geolocation correctly
      // https://github.com/Modernizr/Modernizr/blob/633a5ac/modernizr.js#L478-490
      if ('geolocation' in navigator) {
        $(elt + '_geolocation_waiting').show();
        navigator.geolocation.getCurrentPosition(
          function(position) {
            $(elt + '_geolocation_waiting').hide();
            $(elt + '_range_span').show();
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
  }
};

var mobile = $$('html')[0].hasClassName('mobile');
$$('.geocode_auto_complete').each(function(obj) {
  var name = obj.id;
  C2C.geo[name] = new Autocompleter.Geocode(name,
                    name + '_auto_complete', 
                    'C2C.geo.' + name, {
                      minChars: 3, indicator: 'indicator',
                      keepFocus: !mobile,
                      afterUpdateElement: function(inputField, selectedItem) {
                        $(name + '_lat').value = selectedItem.getAttribute('data-lat');
                        $(name + '_lon').value = selectedItem.getAttribute('data-lon');
                      }
                    });
  if (mobile) {
    var offset;
    obj.observe('focus', function(event) {
      // save current page offset
      offset = window.pageYOffset;
      window.scrollTo(0, 0);
      // move indicator to one better location
      $('indicator').addClassName('auto_complete_pos');
    }).observe('blur', function(event) {
      // scroll back to where we were before selecting input
      window.scrollTo(0, offset);
      // reset indicator position
       $('indicator').removeClassName('auto_complete_pos');
    });
  }
});

})();
