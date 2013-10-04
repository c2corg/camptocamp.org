(function(C2C, $) {

  // hide some fields depending on selected activities
  function hide_outings_unrelated_fields() {
    var show_flags = [
      'outings_glacier',
      'outings_snow_elevation',
      'outings_track',
      'outings_conditions_levels',
      'outings_length',
      'outings_height_diff_down'
    ];
    var show = {};
    for (var i = 0; i < show_flags.length; i++) {
      show[show_flags[i]] = false;
    }

    var activities = $('#activities').val();

    $.each(activities, function (i, activity) {
      if (activity == 1 || activity == 2 || activity == 5 || activity == 7) {
        show.outings_snow_elevation = true;
        show.outings_track = true;
        show.outings_conditions_levels = true;
      }
      if (activity == 1 || activity == 2 || activity == 3 || activity == 7) {
        show.outings_glacier = true;
      }
      if (activity == 1 || activity == 6 || activity == 7) {
        show.outings_length = true;
        show.outings_height_diff_down = true;
      } else {
        if (Math.round($('#outing_length').val()) > 0) {
          show.outings_length = true;
        }
        if (Math.round($('#height_diff_down').val()) > 0) {
          show.outings_height_diff_down = true;
        }
      }
    });

    $.each(show_flags, function(i, flag) {
      $('#' + flag).toggle(show[flag]);
    });
  }

  function disableForm(e) {
    e.stopPropagation();
    C2C.switchFormButtonsStatus($('#editform'), false);
    return false;
  }

  // if some activities are active, ask the user
  // if it is ok:
  // often routes are multi-activity but the outing shouldn't
  function check_outing_activities(e) {
    var activities = $('#activities').val();

    // paragliding
    if (activities.length == 1 && activities[0] == 8) {

      alert(confirm_outing_paragliding_message);
      disableForm(e);
      return false;
    }
    
    if (outing_activities_already_tested) {
      // no need to check activities twice
      return;
    }

    // remove paragliding for next rules
    for (var i = 0; i <activities.length; i++) {
      if (activities[i] != 8) {
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
        return;
      }
    }
    
    // ask for confirmation if nb activities is > 1
    if (!$('#revision').val() && activities.length > 1 &&
        !confirm(confirm_outing_activities_message)) {
      disableForm(e);
      outing_activities_already_tested = true;
    }
  }

  function check_outing_date(e) {
    if (outing_date_already_tested) {
      // no need to check date twice
      return;
    }

    var year = $('#date_year').val();
    var month = $('#date_month').val();
    var day = $('#date_day').val();

    var now = new Date();

    // ask for confirmation if outing date is today and if it is sooner than 14:00
    if (!$('#revision').val() &&
        year == now.getFullYear() &&
        month == (now.getMonth() + 1) &&
        day == now.getDate() &&
        now.getHours() <= 14 &&
        !confirm(confirm_outing_date_message)) {
      disableForm(e);
      outing_date_already_tested = true;
    }
  }

  // be sure to hide fields on startup if needed
  hide_outings_unrelated_fields();

  // register events
  $('#activities').on('change', hide_outings_unrelated_fields);
  $('#editform').submit(function(e) {
    check_outing_activities(e);
    check_outing_date(e);
  });

})(window.C2C = window.C2C || {}, jQuery);
