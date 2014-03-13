(function(C2C, $) {

  // Mountain wilderness contest stuff
  function mw_associate() {
    var indicator = $('#indicator');

    indicator.show();
    $.post('/documents/addAssociation/main_id/' + $('#id').val() +
           '/document_module/articles/document_id/' + C2C.mw_contest_article_id
    ).always(function() {
      indicator.hide();
    }).fail(function(data) {
      mw_contest_associate.attr('checked', false);
      C2C.showFailure();
    });
  }

  function mw_de_associate() {
    var indicator = $('#indicator');

    indicator.show();
    $.post('/documents/removeAssociation/main_oc_id/' + $('#id').val() +
           '/linked_id/' + C2C.mw_contest_article_id + '/type/oc/strict/1'
    ).always(function() {
      indicator.hide();
    }).done(function(data) {
      $(mw_contest_associate).attr('checked', false);
    }).fail(function() {
      $(mw_contest_associate).attr('checked', true);
      C2C.showFailure();
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

  // be sure to hide fields on startup if needed
  switch_mw_contest_visibility();

  // register events
  $('#outing_with_public_transportation').on('change', switch_mw_contest_visibility);
  $('#mw_contest_associate').on('change', switch_mw_contest_association);

})(window.C2C = window.C2C || {}, jQuery);
