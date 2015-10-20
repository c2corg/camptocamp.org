(function(C2C, $) {

  // show/hide is_avalanche tags depending on selected event_type
  function manage_avalanche_fields() {
    var event_type = $('#event_type').val();

    $('#is_avalanche').hide();

    if (!!event_type && ($.inArray("1", event_type) !== -1)) {
      $('#is_avalanche').show();
    };
  }
  
  // show/hide is_impacted tags depending on nb_impacted
  function manage_impacted_fields() {
    var nb_impacted = $('#nb_impacted').val();

    $('#is_impacted').hide();

    if (!!nb_impacted && nb_impacted > 0) {
      $('#is_impacted').show();
    };
  }

  // if some activities are active, ask the user if it is ok:
  // often outings are multi-activity but the xreports shouldn't
  var xreport_activities_already_tested = false;
  function check_xreport_activities() {
    var activities = $('#activities').val();

    if (xreport_activities_already_tested) {
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
      xreport_activities_already_tested = confirm(C2C.confirm_xreport_activities_message);
      return xreport_activities_already_tested;
    }

    return true;
  }

  var xreport_date_already_tested = false;
  function check_xreport_date() {
    if (xreport_date_already_tested) {
      // no need to check date twice
      return true;
    }

    var year = $('#date_year').val();
    var month = $('#date_month').val();
    var day = $('#date_day').val();
    var now = new Date();

    // ask for confirmation if outing date is today and if it is sooner than 14:00
    if (!$('#revision').val() && year == now.getFullYear() && month == (now.getMonth() + 1) &&
        day == now.getDate() && now.getHours() <= 16) {
      xreport_date_already_tested = confirm(C2C.confirm_xreport_date_message);
      return xreport_date_already_tested;
    }

    return true;
  }

  // be sure to hide fields on startup if needed
  manage_avalanche_fields();
  manage_impacted_fields();

  // register events
  $('#event_type').on('change', manage_avalanche_fields);
  $('#nb_impacted').on('change', manage_impacted_fields);
  $('#editform').submit(function(e) {
    if (!check_xreport_activities() || !check_xreport_date()) {
      C2C.switchFormButtonsStatus($('#editform'), false);
      e.preventDefault();
    }
  });

})(window.C2C = window.C2C || {}, jQuery);
