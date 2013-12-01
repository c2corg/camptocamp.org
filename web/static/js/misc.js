(function(C2C, $) {

  // needed on every page
  // FIXME not for mobile version right now, but we hope we will
  // enable editing on mobile version soon

  C2C.submitonce = function(aform) {
    C2C.switchFormButtonsStatus(aform, true);
  };

  C2C.switchFormButtonsStatus = function(form, disable) {
    $(form).find('input[type=submit], input[type=button]').attr('disabled', 'disabled');
  };

  // outings wizard: retrieve route ratings
  C2C.getWizardRouteRatings = function(div_id) {
    // also update link field with selected item
    $('#link').val($('#' + div_id).val());

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

})(window.C2C = window.C2C || {}, jQuery);
