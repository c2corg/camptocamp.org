(function(C2C, $) {

  // Mountain wilderness contest stuff

  function mw_associate() {
    var indicator = $('#indicator');

    indicator.show();
    $.post('/documents/addAssociation/main_id/' + $('#id').val() +
           '/document_module/articles/document_id/' + mw_contest_article_id
    ).always(function() {
      indicator.hide();
    }).done(function(data) {
      $('#ajax_feedback_failure').hide();
    }).fail(function(data) {
      $('#ajax_feedback_failure').show();
      mw_contest_associate.attr('checked', false);
      setTimeout(function() {
        C2C.emptyFeedback("ajax_feedback_failure");
      }, 4000);
    });
  }

  function mw_de_associate() {
    var indicator = $('#indicator');

    indicator.show();
    $.post('/documents/removeAssociation/main_oc_id/' + $('#id').val() +
           '/linked_id/' + mw_contest_article_id + '/type/oc/strict/1'
    ).always(function() {
      indicator.hide();
    }).done(function(data) {
      $(mw_contest_associate).attr('checked', false);
      $('#ajax_feedback_failure').hide();
    }).fail(function() {
      $('#ajax_feedback_failure').show();
      $(mw_contest_associate).attr('checked', true);
      setTimeout(function() {
        C2C.emptyFeedback("ajax_feedback_failure");
      }, 4000);
    });
  }

  function switch_mw_contest_visibility() {
    var mw_contest = $('#mw_contest');
    var mw_contest_associate = $('#mw_contest_associate');

    if (mw_contest.length) {
      if ($('#outing_with_public_transportation').is(':checked')) {
        mw_contest.show();
      } else {
        // hide mw div, uncheck contest checkbox
        mw_contest.hide();

        if (!$('#pseudo_id').length && $(mw_contest_associate).is(':checked')) {
          // de-associated
          mw_de_associate();
        }
      }
    }
  }

  function switch_mw_contest_association() {

    mw_contest_associate = $('#mw_contest_associate');

    if (!$('#pseudo_id').length) {
      if (mw_contest_associate.is(':checked')) {
        // associate
        mw_associate();
      } else {
        // de-associated
        mw_de_associate();
      }
    }
  }

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
  switch_mw_contest_visibility();

  // register events
  $('#outing_with_public_transportation').on('change', switch_mw_contest_visibility);
  $('#mw_contest_associate').on('change', switch_mw_contest_association);
  $('#activities').on('change', hide_outings_unrelated_fields);
  $('#editform').submit(function(e) {
    check_outing_activities(e);
    check_outing_date(e);
  });

})(window.C2C = window.C2C || {}, jQuery);
