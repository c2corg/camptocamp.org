(function(C2C, $) {

  // hide some fields depending on selected public transportation or snow clearance value
  C2C.hide_parkings_unrelated_fields = function() {

    var value = $('#public_transportation_rating').val();
    $('#tp_types, #tp_desc').toggle(value != '3' && value != '0');

    value = $('#snow_clearance_rating').val();    
    $('#snow_desc').toggle(value != '4' && value != '0');
  }

  $(C2C.hide_parkings_unrelated_fields);

})(window.C2C = window.C2C || {}, jQuery);
