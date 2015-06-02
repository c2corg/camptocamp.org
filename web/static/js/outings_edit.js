(function(C2C, $) {

  // hide some parts of the editing form
  // depending on selected activities
  function hide_unrelated_fields() {
    // show/hide data-act-filter tags depending on selected activities
    var activities = $('#activities').val();

    $('[data-act-filter]').hide();

    if (!!activities) $.each(activities, function (i, activity) {
      $('[data-act-filter~='+ activity +']').show();
    });

    // following fields should still be shown under certain conditions
    // (if activity criteria not met)
    if (Math.round($('#outing_length').val()) > 0) {
      $('[data-act-filter~="length"]').show();
    }

    if (Math.round($('#height_diff_down').val()) > 0) {
      $('[data-act-filter~="height_diff_down"]').show();
    }
  }

  // hide avalanche_desc form according to avalanche_date value
  function manage_avalanche_fields() {
    // show/hide avalanche_desc_form depending on selected avalanche_date
    var avalanche_date = $('#avalanche_date').val();

    $('#avalanche_desc_form').hide();
    
    // if option 1 ("no observation") is selected in same time than another one, we unselect option 1
    if (!!avalanche_date && avalanche_date.length >= 2 && (($.inArray("1", avalanche_date) !== -1) || ($.inArray("0", avalanche_date) === -1))) {
      $('#avalanche_date option[value="0"]').prop("selected", false);
      $('#avalanche_date option[value="1"]').prop("selected", false);
      avalanche_date = $('#avalanche_date').val();
    }

    if (!!avalanche_date && ($.inArray("0", avalanche_date) === -1) && ($.inArray("1", avalanche_date) === -1)) {
      $('#avalanche_desc_form').show();
    }
  }

  // if some activities are active, ask the user
  // if it is ok:
  // often routes are multi-activity but the outing shouldn't
  var outing_activities_already_tested = false;
  function check_outing_activities() {
    var activities = $('#activities').val();

    // paragliding cannot be the only activity
    if (activities.length == 1 && activities[0] == 8) {
      alert(C2C.alert_outing_paragliding_message);
      return false;
    }
    
    if (outing_activities_already_tested) {
      // no need to check activities twice
      return true;
    }

    // remove paragliding for next rules
    for (var i = 0; i <activities.length; i++) {
      if (activities[i] == 8) {
        activities.splice(i, 1);
        break;
      }
    }

    if (activities.length == 2) {
      activities.sort();
      var act_0 = activities[0];
      var act_1 = activities[1];

      if ((act_0 == 2 && act_1 == 3) ||
          (act_0 == 2 && act_1 == 5) ||
          (act_0 == 2 && act_1 == 7) ||
          (act_0 == 3 && act_1 == 4) ||
          (act_0 == 3 && act_1 == 6) ||
          (act_0 == 4 && act_1 == 6)) {
        // no need to check activities for these pairs
        return true;
      }
    }

    // ask for confirmation if nb activities is > 1
    if (!$('#revision').val() && activities.length > 1) {
      outing_activities_already_tested = confirm(C2C.confirm_outing_activities_message);
      return outing_activities_already_tested;
    }

    return true;
  }

  var outing_date_already_tested = false;
  function check_outing_date() {
    if (outing_date_already_tested) {
      // no need to check date twice
      return true;
    }

    var year = $('#date_year').val();
    var month = $('#date_month').val();
    var day = $('#date_day').val();
    var now = new Date();

    // ask for confirmation if outing date is today and if it is sooner than 14:00
    if (!$('#revision').val() && year == now.getFullYear() && month == (now.getMonth() + 1) &&
        day == now.getDate() && now.getHours() <= 14) {
      outing_date_already_tested = confirm(C2C.confirm_outing_date_message);
      return outing_date_already_tested;
    }

    return true;
  }

  // check exotic shoesage elevation
  var snow_elevation_already_tested = false;
  var access_elevation_init = $('#access_elevation').val();
  function check_snow_elevation() {
    if (snow_elevation_already_tested) {
      // no need to check shoesage elevation twice
      return true;
    }

    var activities = $('#activities').val();
    var access_elevation = $('#access_elevation').val();
    var up_snow_elevation = $('#up_snow_elevation').val();
    var down_snow_elevation = $('#down_snow_elevation').val();
    var now = new Date();
    var month = now.getMonth() + 1;

    // ask for confirmation if shoesage elevation is equal to access_elevation
    if (!$('#revision').val()) {
      if (   !!access_elevation_init
          && (    $.inArray (1, activities)
              ||  $.inArray (2, activities)
              ||  $.inArray (5, activities)
              ||  $.inArray (7, activities)
             )
          && access_elevation    == access_elevation_init
          && up_snow_elevation   == access_elevation_init
          && down_snow_elevation == access_elevation_init
         ) {
         if (   (access_elevation < 1200 && month >= 3 && month <= 11)
             || (access_elevation < 1400 && month == 4)
             || (access_elevation < 1600 && month == 5)
             || (access_elevation < 2000 && month == 6)
             || (access_elevation < 2400 && month == 7)
             || (access_elevation > 1600 && (month <= 2 || month >= 12))
             || (access_elevation > 1800 && month == 3)
             || (access_elevation > 2000 && (month <= 4 || month >= 11))
             || (access_elevation > 2200 && month == 5)
             || (access_elevation > 2600 && month == 6)
            ) {
           snow_elevation_already_tested = confirm(C2C.confirm_snow_elevation_message);
           return snow_elevation_already_tested;
         }
       }
    }

    return true;
  }

  // be sure to hide fields on startup if needed
  hide_unrelated_fields();
  manage_avalanche_fields();

  // register events
  $('#activities').on('change', hide_unrelated_fields);
  $('#avalanche_date').on('change', manage_avalanche_fields);
  $('#editform').submit(function(e) {
    if (!check_outing_activities() || !check_outing_date() || !check_snow_elevation()) {
      C2C.switchFormButtonsStatus($('#editform'), false);
      e.preventDefault();
    }
  });

})(window.C2C = window.C2C || {}, jQuery);
