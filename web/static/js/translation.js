GoogleTranslator = {
  init: function() {
    // add goog-trans-section class to section to translate (the i18n part of the doc)
    $$('.article_contenu')[0].addClassName('goog-trans-section');
    // add translation button
    $$('.article_contenu')[0].insert({top: new Element('div', {'class': 'goog-trans-control'})});
    // retrieve interface culture
    var culture = document.documentElement.lang;
    // asynchronously load google translator js
    var a = document.createElement('script'), h = document.getElementsByTagName('head')[0];
    a.async = 1;
    a.src = '//translate.google.com/translate_a/element.js?cb=GoogleTranslator.onready&ug=section&hl=' + culture;
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
Event.observe(window, 'load', function() { GoogleTranslator.init(); });
