(function (C2C, $) {

  C2C.GoogleTranslator = {

    init: function() {
      var section = $('.article_contenu').first();
      // add goog-trans-section class to section to translate (the i18n part of the doc)
      // and add translation button
      section.addClass('goog-trans-section').prepend('<div class="goog-trans-control"></div>');
      // retrieve interface culture
      var culture = document.documentElement.lang;
      // asynchronously load google translator js
      var a = document.createElement('script'), h = document.getElementsByTagName('head')[0];
      a.async = 1;
      a.src = '//translate.google.com/translate_a/element.js?cb=C2C.GoogleTranslator.onready&ug=section&hl=' + culture;
      h.appendChild(a);
    },

    onready: function() { // called once js is loaded
      new google.translate.SectionalElement({
        sectionalNodeClassName: 'goog-trans-section',
        controlNodeClassName: 'goog-trans-control',
        background: '#ffeecc'
      }, 'google_sectional_element');
    }
  };

  // init once dom loaded
  $(C2C.GoogleTranslator.init);

})(window.C2C = window.C2C || {}, jQuery);
