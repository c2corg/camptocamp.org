(function(C2C, $) {

  var activities = [];

  // for some search inputs like date selection, we hide some parts
  // depending on the first field value
  // e.g. only display one input for selecting summits higher than 4000m,
  // two inputs for selecting summits between 2000m and 3000m
  C2C.update_on_select_change = function(field, optionIndex) {
    var index = $('#' + field + '_sel').val();

    if (index == '0' || index === ' ' || index == '-' || index >= 4) {

      $('#' + field + '_span1').hide();
      $('#' + field + '_span2').hide();

      if (optionIndex >= 4) {
        if (index == 4) {
          $('#' + field + '_span3').show();
        } else {
          $('#' + field + '_span3').hide();
        }
      }

    } else {

      $('#' + field + '_span1').show();

      if (index == '~' || index == 3) {
        $('#' + field + '_span2').show();
      } else {
        $('#' + field + '_span2').hide();
      }

      if (optionIndex >= 4) {
        $('#' + field + '_span3').hide();
      }

    }
  };


  // see hide_unrelated_fields() in routes.js
  // hide some fields depending onf the activity selected
  C2C.hide_unrelated_filter_fields = function(current_activity) {

    if (activities.indexOf(current_activity) != -1) {
       // if activity is already selected, unselect it
       activities = activities.without(current_activity);
    } else {
       // else add it to the selection
       activities.push(current_activity);
    }

    var show_flags = [
      'ski',
      'ski_snow_mountain',
      'ski_snow_mountain_rock',
      'ski_snow_mountain_rock_ice',
      'snow_ice',
      'snow_mountain_rock_ice',
      'snow_mountain_ice',
      'rock_mountain',
      'hiking',
      'snowshoeing'
    ];

    var show = {};
    for (var i = 0; i < show_flags.length; i++) {
      show[show_flags[i]] = false;
    }
    show.snow = false;

    //activities.each(function(activity)
    $.each(activities, function(i, activity) {
      switch (activity) {
        case 1: // skitouring
          show.ski = true;
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          break;

        case 2: // snow_ice_mixed
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.snow = true;
          show.snow_ice = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          break;

        case 3: // mountain_climbing
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          show.rock_mountain = true;
          break;

        case 4: // rock_climbing
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.snow_mountain_rock_ice = true;
          show.rock_mountain = true;
          break;

        case 5: // ice_climbing
          show.ski_snow_mountain_rock_ice = true;
          show.snow_ice = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          break;

        case 6: // hiking
          show.hiking = true;
          break;

        case 7: // snowshoeing
          show.snowshoeing = true;
        }
    });

    // hide / show sections
    $.each(show_flags, function(i, flag) {
      $('#' + flag + '_fields').toggle(show[flag]);
    });

    // route configuration
    var conf = document.getElementById('conf');
    if (conf && show.ski_snow_mountain_rock)
    {
      var select_size = 6;
      if (show.snow) {
        $('#conf option:eq(4)').show();
        $('#conf option:eq(5)').show();
      } else {
        $('#conf option:eq(4)').hide();
        $('#conf option:eq(5)').hide();
        select_size -= 2;
      }

      conf.size = select_size;
    }
  };

  C2C.changeSelectSize = function(id, up_down) {
    var select = $('#' + id);
    var height = select.height();

    if (up_down) {
      height += 150;
    } else {
      height = Math.max(100, height - 150);
    }

    select.height(height);
  };

})(window.C2C = window.C2C || {}, jQuery);
