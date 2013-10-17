(function(C2C, $) {

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
      }).fail(function(data) {
        C2C.showFailure(data.responseText);
      }).done(function(data) {
        $('#' + type_linked_id + ', .' + type_linked_id).hide();
      });
    }

    return false;
  };

})(window.C2C = window.C2C || {}, jQuery);
