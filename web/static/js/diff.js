/**
 * History page diff radio buttons behavior.
 * Originally inspired from Mediawiki code.
 * http://www.mediawiki.org/
 */
(function($) {

  // check selection and tweak visibility/class onclick
  function diffcheck() {
    var ntr = []; // the tr where the "new" radio is checked
    var otr = []; // the tr where the "old" radio is checked

    $('#pagehistory tr').each(function() {

      var inputs = $(this).find('input[type=radio]');

      if (inputs.is(':checked')) { // this row has a checked radio button

        if (otr.length) { // it's the second checked radio
          if (inputs.last().is(':checked')) {
            otr.addClass('selected');
            return;
          }
        } else if (inputs.first().is(':checked')) {
          return;
        }

        if (inputs.first().is(':checked')) {
          ntr = $(this);
        }

        if (!otr.length) {
          inputs.first().css('visibility', 'hidden');
        }

        if (ntr.length) {
          inputs.last().css('visibility', 'hidden');
        }

        $(this).addClass('selected');
        otr = $(this);

      } else { // no radio is checked in this row

        if (!otr.length) {
          inputs.first().css('visibility', 'hidden');
        } else {
          inputs.first().css('visibility', 'visible');
        }

        if (ntr.length) {
          inputs.last().css('visibility', 'hidden');
        } else {
          inputs.last().css('visibility', 'visible');
        }

      }
    });
  }

  // run page history stuff once dom loaded
  $(function() {
    $('#pagehistory tr input[type=radio]').click(diffcheck);
  });

})(jQuery);
