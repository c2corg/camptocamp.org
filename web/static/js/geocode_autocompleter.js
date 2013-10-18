(function(C2C, $) {

  C2C.update_around_on_select_change = function(elt) {
    var index = $('#' + elt + '_sel')[0].selectedIndex;

    // reset fields and hide all inner spans
    $('#' + elt + '_lat, #' + elt + '_lon').val('');
    $('#' + elt + '_range_span').show();
    $('#' + elt + '_geocode, #' + elt + '_geolocation_not_supported, #' + elt +
           '_geolocation_waiting, #' + elt + '_geolocation_failed').hide();

    if (index === 0) {
      $('#' + elt + '_span').hide();
    } else {
      // display high level span
      $('#' + elt + '_span').show();

      // display only relevant inner span
      if (index === 1) { // geocode autocompleter
        $('#' + elt + '_geocode').show();
      } else if (index === 2) { // user geolocalization
        $('#' + elt + '_range_span').hide();
        // detect geolocation correctly
        // https://github.com/Modernizr/Modernizr/blob/633a5ac/modernizr.js#L478-490
        if ('geolocation' in navigator) {
          $('#' + elt + '_geolocation_waiting').show();
          navigator.geolocation.getCurrentPosition(
            function(position) {
              $('#' + elt + '_geolocation_waiting').hide();
              $('#' + elt + '_range_span').show();
              $('#' + elt + '_lat').val(position.coords.latitude);
              $('#' + elt + '_lon').val(position.coords.longitude);
            },
            function(error) {
              $('#' + elt + '_geolocation_waiting').hide();
              $('#' + elt + '_geolocation_' + (error.code === 1 ? 'denied' : 'failed')).show();
            });
        } else {
          // geolocation not supported by browser
          $('#' + elt + '_geolocation_not_supported').show();
        }
      }
    }
  };

  function getSuggestions(q) {
    var dfd = new $.Deferred();

    // if the element has class geonames, we use this service, else we use nominatim (http://wiki.openstreetmap.org/wiki/Nominatim)
    var service = this.el.hasClass('geonames') ? 'geonames' : 'nominatim';
    
    // the 'no result' translated string is found as a dataset of the input element
    var noresult = function() {
      dfd.resolve('<ul><div class="feedback">' + this.element.getAttribute('data-noresult') + '</div></ul>');
    };
                                          
    var request;
    if (service === 'geonames') {
      request = 'http://api.geonames.org/searchJSON?maxRows=10&featureClass=P&featureClass=T' +
                '&username=c2corg&lang=' + document.documentElement.lang + '&name_startsWith=' +
                encodeURIComponent(q) + '&callback=?';
    } else {
      request = 'http://nominatim.openstreetmap.org/search?format=json&limit=10' +
                '&email=dev@campto' + 'camp.org&q=' + encodeURIComponent(q) + '&json_callback=?';
    }

    $.getJSON(request).done(function(json) {
      var ul = '<ul>';
      var place;

      if (service === 'geonames') { // geonames
        if (json.totalResultsCount > 0) {
          for (place in json.geonames) {
            if (json.geonames.hasOwnProperty(place)) {
              ul += '<li data-lat="' + json.geonames[place].lat + '" data-lon="' + json.geonames[place].lng +
                    '">' + json.geonames[place].name + '<br /><em class="informal">[' + json.geonames[place].adminName1 +
                    ' - ' + json.geonames[place].countryName + ']</em></li>';
            }
          }
          dfd.resolve(ul + '</ul>');
        } else {
          noresult();
        }

      } else { // nominatim
        if (json.size() > 0) {
          for (place in json) {
            if (json.hasOwnProperty(place)) {
              ul += '<li data-lat="' + json[place].lat + '" data-lon="' + json[place].lon + '">' +
                    json[place].display_name + '</li>';
            }
          }
          dfd.resolve(ul + '</ul>');
        } else {
          noresult();
        }
      }
    }).fail(function() {
      dfd.reject('<ul><div class="feedback">Error</div></ul>');
    });

    return dfd.promise();
  }

  var input = $('.geocode_auto_complete'), name = input[0].id;
  input.c2cAutocomplete({
    getService: getSuggestions
  }).on('itemselect', function(event, item) {
    $('#' + name + '_lat').val(item.getAttribute('data-lat'));
    $('#' + name + '_lon').val(item.getAttribute('data-lon'));
  });

  if ($('html').hasClass('mobile')) {
    var offset, indicator = $('#indicator');
    $(this).focus(function() {
      // save current page offset
      offset = window.pageYOffset;
      window.scrollTo(0, 1);
      // move indicator to one better location
      indicator.addClass('auto_complete_pos');
    }).blur(function() {
      // scroll back to where we were before selecting input
      window.scrollTo(0, offset);
      // reset indicator position
      indicator.removeClass('auto_complete_pos');
    });
  }

})(window.C2C = window.C2C || {}, jQuery);
