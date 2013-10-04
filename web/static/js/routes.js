(function(C2C, $) {

  // hide some parts of the editing form
  // depending on selected activities 
  C2C.hide_unrelated_fields = function() {

    var show = {};
    var show_flags = [
      'data',
      'ski',
      'ski_snow',
      'ski_snow_mountain',
      'ski_snow_mountain_rock',
      'ski_snow_mountain_rock_ice',
      'ski_snow_mountain_hiking',
      'snow_ice',
      'rock_mountain',
      'snow_mountain_rock_ice',
      'snow_mountain_ice',
      'hiking',
      'hiking2',
      'pack_ski',
      'pack_snow_easy',
      'pack_mountain_easy',
      'pack_rock_bolted',
      'pack_ice',
      'pack_hiking',
      'snowshoeing'
    ];

    for (var i = 0; i < show_flags.length; i++) {
      show[show_flags[i]] = false;
    }
    show.snow = false;

    var activities = $('#activities').val();
    show.data = !!activities.length;

    $.each(activities, function (i, activity) {
      switch (activity) {

        case '1': // skitouring
          show.ski = true;
          show.ski_snow = true;
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.ski_snow_mountain_hiking = true;
          show.pack_ski = true;
          break;

        case '2': // snow_ice_mixed
          show.ski_snow = true;
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.ski_snow_mountain_hiking = true;
          show.snow = true;
          show.snow_ice = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          show.pack_snow_easy = true;
          show.pack_ice = true;
          break;

        case '3': // mountain_climbing
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.ski_snow_mountain_hiking = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          show.rock_mountain = true;
          show.pack_mountain_easy = true;
          break;

        case '4': // rock_climbing
          show.ski_snow_mountain_rock = true;
          show.ski_snow_mountain_rock_ice = true;
          show.snow_mountain_rock_ice = true;
          show.rock_mountain = true;
          show.pack_rock_bolted = true;
          break;

        case '5': // ice_climbing
          show.ski_snow_mountain_rock_ice = true;
          show.snow_ice = true;
          show.snow_mountain_rock_ice = true;
          show.snow_mountain_ice = true;
          show.pack_ice = true;
          break;

        case '6': // hiking
          show.ski_snow_mountain_hiking = true;
          show.hiking = true;
          show.hiking2 = true;
          show.pack_hiking = true;
          break;

        case '7': // snowshoeing
          show.ski_snow_mountain = true;
          show.ski_snow_mountain_rock = true;
          show.snowshoeing = true;
          show.ski_snow = true;
          break;

        default :
          show.data = false;
      }
    });

    $.each(show_flags, function(i, flag) {
      $('#' + flag + '_fields').toggle(show[flag]);
    });
    
    if (show.ski_snow_mountain_rock) {
      var select_size = 7;
      if (show.snow) {
        $('#configuration option:eq(5)').show();
        $('#configuration option:eq(6)').show();
      } else {
        $('#configuration option:eq(5)').hide();
        $('#configuration option:eq(6)').hide();
        select_size -= 2;
      }
        
      $('i#configuration').attr('size', select_size);
    }
    
    $('input.rlineb').toggle(show.snow_mountain_rock_ice);
  };

  // be sure to hide fields once dom loaded
  $(C2C.hide_unrelated_fields);

})(window.C2C = window.C2C || {}, jQuery);
