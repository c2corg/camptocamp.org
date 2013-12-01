(function($) {

  // hide some parts of the editing form
  // depending on selected activities 
  function hide_unrelated_fields() {

    var activities = $('#activities').val();

    // show/hide data-act-filter tags depending on selected activities
    $('[data-act-filter]').hide();

    $('[data-act-filter="none"]').toggle(!!activities);

    if (!!activities) $.each(activities, function (i, activity) {
      $('[data-act-filter~='+ activity +']').show();
    });

    // some configuration should only be available if
    // activity 2 (snow, ice, mixed) is selected
    // not that not all browser allow to hide option tags, so we also
    // disable them
    var select = $('#configuration');
    var options = select.find('option:eq(5), option:eq(6)');
    if (activities && $.inArray("2", activities) !== -1) {
      var select_size = 7;
      options.prop('disabled', false).show();
    } else {
      var select_size = 5;
      options.prop('disabled', true).hide();
    }
    select.attr('size', select_size);

  }

  // check fields state every time the activity selection changes
  $('#activities').on('change', hide_unrelated_fields);

  // be sure to hide fields once dom loaded
  hide_unrelated_fields();

})(jQuery);
