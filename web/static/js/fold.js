(function(C2C, $) {

  /**
   * Set a pref_value for 'fold' cookie
   */
  function setFoldCookie(position, value) {
    value = value ? 't' : 'f';
    var date = new Date();
    date.setFullYear(date.getFullYear()+1);

    // retrieve current cookie value
    var cookie_value = /fold=([tfx]{20});/.exec(document.cookie);
    cookie_value = cookie_value ? cookie_value[1] :  'xxxxxxxxxxxxxxxxxxxx'; // size 20

    // update position with value
    cookie_value = cookie_value.substr(0, position) +  value + cookie_value.substr(position + 1);
    document.cookie = 'fold=' + cookie_value + '; expires=' + date.toGMTString();
  }

  /**
   * Save fold pref in cookie and profile
   */
  function registerFoldStatus(pref_name, cookie_position, opened) {

    // if user logged, save pref in profile
    if ($('#name_to_use').length) {
      $.post('/users/savepref', {
        'name': pref_name + '_home_status',
        'value': !!opened
      });
    }

    // save pref in cookie
    setFoldCookie(cookie_position, opened);
  }

  /**
   * Hide or show an home section
   */
  C2C.toggleHomeSectionView = function(container_id, cookie_position) {
    var div = $('#' + container_id + '_section_container');
    var title = $('#' + container_id + '_section_title, #' + container_id + '_toggle');

    var is_open = div.is(':visible');

    if (!is_open) {
      title.attr('title', open_close[1]);
      if (/MSIE [67].0/.exec(navigator.userAgent)) {
        div.css('display', 'block'); // for ie6-7 only
      }
    } else {
      title.attr('title', open_close[0]);
    }

    div.slideToggle(600);

    registerFoldStatus(container_id, cookie_position, !is_open);

    $('#' + container_id + ' .nav_box_top').toggleClass('small');
  };


  /**
   * Add some properties and observers to have '+' and '-' pictos for folding sections
   */
  function initHomeSections() {

    $('.nav_box_title, .home_title').mouseover(function() {
      var img = $(this).children(':first');
      var savedClass = img.attr('class').split(' ')[1];
      img.data('savedClass', savedClass)
        .removeClass(savedClass)
        .addClass('picto_' + ($(this).next().is(':visible') ? 'close' : 'open'));
    }).mouseout(function() {
      var img = $(this).children(':first');
      img.removeClass('picto_close picto_open')
        .addClass(img.data('savedClass'));
    });
  }

  /**
   * Hide or show a container
   */
  C2C.toggleView = function(container_id) {
    var alt, sign;
    var div = $('#' + container_id + '_section_container');
    
    var div_visible = div.is(':visible');

    if (div_visible) {
      alt = open_close[0];
      sign = '+';
    } else {
      alt = open_close[1];
      sign = '-';
    }

    $('#' + container_id + '_toggle').attr('alt', sign)
      .toggleClass('picto_open picto_close');
    $('#' + container_id + '_section_title').attr('title', alt);
    $('#tip_' + container_id).html('[' + alt + ']');
    div.slideToggle(400);

    // FIXME maybe we should make this less specific... or move this logic somewhere else
    // specific behaviour for the map and elevation profile
    if (container_id == 'map_container') {
      registerFoldStatus(container_id, 15, !div.visible());

      // load map if needed
      // - presence of mapLoading div shows that map has not been created yet
      // - if map_load_async is defined, the map js should be retrieved asynchonously
      // - the delay is to ensure that div is opened and ready for initiating the map
      if (!div_visible && $('#mapLoading').length) {
        setTimeout(typeof map_load_async !== 'undefined' ? asyncloadmap : map_init, 600);
      }

      // also toggle the c2clayer widget
      $('.x-window.x-resizable-pinned').toggle();

    } else if (container_id == 'elevation_profile_container') {
      if (!div_visible && !div.hasClass('profile_loaded')) {
        c2c_load_elevation_profile();
      }
    }
  };

  /**
   * Hide or show a single box (like weather box in outings)
   */
  C2C.toggleBox = function(box_id) {
    var alt, sign;
    var div = $('#' + box_id + '_box');

    if (!div.is(':visible')) {
      alt = open_close[1];
      sign = '-';
    } else {
      alt = open_close[0];
      sign = '+';
    }
 
    $('#toggle_' + box_id).attr('alt', sign)
      .toggleClass('picto_open_light picto_close_light');
    $('#' + box_id + '_box_title').attr('title', alt);

    div.slideToggle(200);
  };

  // show / hide routes (e.g. in summit pages) depending on user
  // selected activities

  function toggleRoutes(activity_id) {
    $('#' + activity_id).toggleClass('picto_open_light picto_close_light'); 
    $('.child_routes.' + activity_id).toggle(200);
  }

  C2C.handleRoutes = function() {
    var activity_id = $(this).attr('class').split(' ').pop();
    toggleRoutes(activity_id);
  };

  function showRoutes(activity_id) { // TODO refactoring needed :)
    toggleRoutes(activity_id);
  }

  function hideRoutes(activity_id) {
    toggleRoutes(activity_id);
  }

  function showAllRoutes() {
    var activities = $('#routes_section_container .title2');

    activities.each(function() {
      var activity_id = $(this).attr('class').split(' ').pop();
      showRoutes(activity_id);
    });
    window.location.href = '#routes_summary';
  }

  function hideAllRoutes() {
    var activities = $('#routes_section_container .title2');
    activities.each(function() {
      var activity_id = $(this).attr('class').split(' ').pop();
      hideRoutes(activity_id);
    });
  }

  // link to specific part of routes list when grouped by activity
  // TODO not sure this is useful (hashtags already in html ??)
  C2C.linkRoutes = function(activity_id) {
    showRoutes(activity_id);
    var container = $('#routes_section_container .title2.' + activity_id);
    var anchor = container.attr('id');

    if (!anchor) {
      anchor = Math.random().toString(36).substr(2, 5); // TODO is that necessary?
      container.attr('id', anchor);
    }
    window.location.href = '#' + anchor;
  };

  /**
   * This function is called to hide routes depending on their activities and the user prefs
   */
  C2C.initRoutes = function() {
    var activities_to_show = $('#quick_switch').attr('class').split(' ');
    if (activities_to_show.length) {
      $('.child_routes').each(function() {
        var activity_id = $(this).attr('class').split(' ').pop();
        if ($.inArray(activity_id, activities_to_show)) {
          $(this).hide();
          $('#' + activity_id).toggleClass('picto_close_light picto_open_light');
        }
      });
    }
  };

  // empty ajax feedback div
  C2C.emptyFeedback = function(aff) {
    $(aff).html('').hide();
  };

  // toggle association form 
  C2C.toggleForm = function(form_id) {
    $('#' + form_id + '_association').toggleClass('hide show');
  };

  function initRoutes() {
    // handle routes display
    $('#routes_section_container .title2').click(C2C.handleRoutes);
    $('#close_routes').click(hideAllRoutes);
    $('#open_routes').click(showAllRoutes);
  }


  // splitter and left navigation stuff

  /**
   * 0: splitter not displayed
   * 1: splitter will be displayed when timeout expires
   * 2: splitter displayed
   * 3: splitter will be hidden when timeout expires
   */
  var splitter_status = 0;

  function highlight_splitter(ypos) {
    $('#splitter, .ombre_haut_corner_left, .ombre_bas_corner_left').toggleClass('hl');
    $('body').append('<div id="splitter_arrow"></div>');
    set_splitter_pos(ypos);
    splitter_status = 2;
  }

  function unhighlight_splitter() {
    $('#splitter, .ombre_haut_corner_left, .ombre_bas_corner_left').toggleClass('hl');
    $('#splitter_arrow').remove();
    splitter_status = 0;
  }

  function set_splitter_pos(ypos) {
    var arrow = $('#splitter_arrow');
    var splitter = $('#splitter');
    var offset = splitter.offset().left;

    if (!arrow.length) { return; }

    if (splitter.hasClass('maximize')) {
      arrow.addClass('maximize');
      arrow.css('left', offset + 20 + 'px');
    } else {
      arrow.css('left', offset - 10 + 'px');
    }
    arrow.css('top', ypos + 'px');
  }

  function move_splitter_arrow(e) {
    set_splitter_pos(e.pageY);
  }

  function toggleHomeNav(savestatus) {
    // no left menu folding for ie6-7
    if (/MSIE [67].0/.exec(navigator.userAgent)) return;

    var wrapper = $('#wrapper_context');
    var splitter = $('#splitter');
    var wide = wrapper.hasClass('no_nav');

    $('.nav_box').toggle();
    wrapper.toggleClass('no_nav');
    splitter.toggleClass('maximize')
      .attr('title', wide ? open_close[3] : open_close[2]);

    unhighlight_splitter();

    if (savestatus) {
      registerFoldStatus(nav_status_string, nav_status_cookie_position, wide);
    }
  }

  function toggleNav(savestatus) {
    // no left menu folding for ie6-7
    if (/MSIE [67].0/.exec(navigator.userAgent)) return;

    var content_box = $('#content_box');
    var splitter = $('#splitter');
    var wide = content_box.hasClass('wide');

    content_box.toggleClass('wide');
    splitter.toggleClass('maximize')
      .attr('title', splitter.data(wide ? 'title-reduce' : 'title-enlarge'));

    unhighlight_splitter();

    if (savestatus) {
      registerFoldStatus(nav_status_string, nav_status_cookie_position, wide);
    }
  }
  
  function initSplitter() {
    var splitter = $('#splitter');
    var splitter_timer = null;

    // handle splitter (but not for ie6-7)
    if (/MSIE [67].0/.exec(navigator.userAgent)) return;

    // init splitter title and class
    if ($('#content_box').hasClass('wide') || $('#wrapper_context').hasClass('no_nav')) {
      splitter.addClass('maximize').attr('title', splitter.data('title-enlarge'));
    } else {
      splitter.attr('title', splitter.data('title-reduce'));
    }

    // add event handlers on splitter
    splitter.mouseover(function(e) {
      switch (splitter_status) {
        case 3:
          clearTimeout(splitter_timer);
          splitter_status = 2;
          break;
        case 0:
          var _ypos = e.pageY;
          splitter_timer = setTimeout(function() {
            highlight_splitter(_ypos);
          }, 50);
          splitter_status = 1;
          break;
       }
    }).mouseout(function() {
      switch (splitter_status) {
        case 1:
          clearTimeout(splitter_timer);
          splitter_status = 0;
          break;
        case 2:
          splitter_timer = setTimeout(unhighlight_splitter, 600);
          splitter_status = 3;
          break;
        }
      }).mousemove(move_splitter_arrow)
      .click($('#wrapper_context').hasClass('home') ? toggleHomeNav : toggleNav);
  }

  // initialization

  $(function() {
    initHomeSections();
    initRoutes();
    initSplitter();

    // TODO move that elsewhere?
    // select all in list pages
    $('#select_all').change(function() {
      $('table.list td input[type=checkbox]').attr('checked', $(this).is(':checked'));
    });

    // TODO idem
    // when clicking in codes in image pages, select whole input
    $('input.code').click(function() { $(this).select(); });

  });

})(window.C2C = window.C2C || {}, jQuery);
