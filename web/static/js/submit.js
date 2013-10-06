(function(C2C, $) {

  C2C.submitonce = function(aform) {
    C2C.switchFormButtonsStatus(aform, true);
  };

  C2C.switchFormButtonsStatus = function(form, disable) {
    $(form).find('input[type=submit], input[type=button]').attr('disabled', 'disabled');
  };

  // outings wizard: retrieve route ratings
  C2C.getWizardRouteRatings = function(div_id) {
    var indicator = $('#indicator');

    indicator.show();

    $.get('/routes/getratings', {
      'id': $('#' + div_id).val()
    }).always(function() {
      indicator.hide();
    }).done(function(data) {
      $('#' + div_id + '_descr').html(data);
      $('#wizard_' + div_id + '_descr').show();
    }).fail(function() {
      $('#wizard_' + div_id + '_descr').hide();
    });
  };

  // unlink a document
  C2C.remLink = function(link_type, main_id, linked_id, main_doc, strict) {

    if (confirm(confirm_msg)) {
      var type_linked_id = link_type + '_';
      if (main_doc) {
        type_linked_id = type_linked_id + linked_id;
      } else {
        type_linked_id = type_linked_id + main_id;
      }

      var indicator = $('#indicator');

      indicator.show();
      $('#del_' + type_linked_id).hide();

      $.post('/documents/removeAssociation/main_' + link_type + '_id/' + main_id +
             '/linked_id/' + linked_id + '/type/' + link_type + '/strict/' + strict
      ).always(function() {
        indicator.hide();
        setTimeout(function() {
          C2C.emptyFeedback('ajax_feedback_failure');
        }, 4000);
      }).fail(function(data) {
        $('#ajax_feedback_failure').html(data).show();
      }).done(function(data) {
        $('#' + type_linked_id).html(data);
        $('#' + type_linked_id + ', .' + type_linked_id).hide();
      });
    }

    return false;
  };

})(window.C2C = window.C2C || {}, jQuery);
