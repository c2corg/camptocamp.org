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

  // be sure to hide fields on startup if needed
  hide_unrelated_fields();

  // register events
  $('#activities').on('change', hide_unrelated_fields);
  $('#editform').submit(function(e) {
    if (!check_outing_activities() || !check_outing_date()) {
      C2C.switchFormButtonsStatus($('#editform'), false);
      e.preventDefault();
    }
  });

})(window.C2C = window.C2C || {}, jQuery);
