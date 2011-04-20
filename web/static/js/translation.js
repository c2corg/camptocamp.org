GoogleTranslation = {

  set_params: function() {
    GoogleTranslation.translate_limit = 1500;
    GoogleTranslation.original_texts = null;
    GoogleTranslation.translate_wait = '<span class="translate_wait translate_button">' +
                                       GoogleTranslation.i18n[2] + '</span>';
    GoogleTranslation.translate_button = '<span class="translate_button"><a href="#" ' +
                                         'onclick="GoogleTranslation.translate();return false;">' +
                                         GoogleTranslation.i18n[0] + '</a></span>';
    GoogleTranslation.untranslate_button = '<span class="translate_button"><a href="#" ' +
                                           'onclick="GoogleTranslation.untranslate();return false;">' +
                                           GoogleTranslation.i18n[1] + '</a></span>';
  },

  init_buttons: function() {
    GoogleTranslation.set_params();
    $$('.switch_lang').each(function(o) {
      new Insertion.Bottom(o, GoogleTranslation.translate_button);
    });
  },

  translate: function() {
    $$('.translate_button').invoke('replace', GoogleTranslation.translate_wait);
    $$('.section_subtitle, .toc_link').invoke('addClassName', 'notranslate');
    GoogleTranslation._translate($$('.article_contenu')[0]);
  },

  untranslate: function() {
    GoogleTranslation._untranslate($$('.article_contenu')[0]);
    $$('.translate_button').invoke('replace', GoogleTranslation.translate_button);
    $$('.gBranding').invoke('remove');
  },

  error: function(msg) {
    $$('.translate_wait').invoke('update', 'Error: ' + msg);
  },

  _translate: function(obj) {
    var content = GoogleTranslation.handle_email(obj.innerHTML);
    if (escape(content.length) < GoogleTranslation.translate_limit) {
      GoogleTranslation._google_translate(content, GoogleTranslation.language_from, GoogleTranslation.language_to,
        function(result) {
          if (!result.error) {
            GoogleTranslation.show_translation(obj, result.data.translations[0].translatedText);
          } else {
            GoogleTranslation.error(result.error);
          }
        });
    } else { // text is too long
      var strings = GoogleTranslation.cut(content, '</p>').flatten();
      GoogleTranslation.conc_total = strings.length;
      GoogleTranslation.conc_translated = 0;
      GoogleTranslation.conc_strings = [];
      strings.each(function(s, index) {
        GoogleTranslation._google_translate(s, GoogleTranslation.language_from, GoogleTranslation.language_to,
          function(result) {
            if (!result.error) {
              GoogleTranslation.add_partial_translation(obj, result.data.translations[0].translatedText, index);
            } else {
              GoogleTranslation.error(result.error);
            }
          });
      });
    }
  },

  _google_translate: function(content, from, to, callback) {
    // we use an intermediate callback, since it must be called
    // directly by the response and we attach some variables
    var rnd = '_' + Math.round(Math.random() * 1000000000);
    GoogleTranslation[rnd] = function(response) {
      callback(response);
    };

    // perform the REST call
    var url = GoogleTranslation.base_url + '&callback=GoogleTranslation.' + rnd + '&source=' +
              from + '&target=' + to + '&q=' + escape(content);
    var head = $$('head')[0];
    var script = new Element('script', { type: 'text/javascript',
                                         async: true,
                                         src:   url });
    head.appendChild(script);
  },

  show_translation: function(obj, translated_text) {
    var translated_div_content = '<div class="article_contenu">'+translated_text+'</div>';
    var original_div = obj;
    new Insertion.After(original_div, translated_div_content);
    original_texts = original_div;
    original_div.remove();

    $$('.translatable').each(function(o) {
      // TODO add 'provided by' text for 'better' branding?
      Element.insert(o, { bottom: new Element('img', { src: 'http://www.google.com/uds/css/small-logo.png' }) });
    });

    new Effect.Highlight('description_section_container');

    $$('.translate_button').invoke('replace', GoogleTranslation.untranslate_button);
  },

  add_partial_translation: function(obj, translated_part, index) {
    (GoogleTranslation.conc_translated)++;
    GoogleTranslation.conc_strings[index] = translated_part;
    if (GoogleTranslation.conc_translated == GoogleTranslation.conc_total) {
      var translated_text = '';
      for (var i = 0, len = GoogleTranslation.conc_strings.length; i < len; ++i) {
        translated_text += GoogleTranslation.conc_strings[i];
      }
      GoogleTranslation.show_translation(obj, translated_text);
    }
  },

  _untranslate: function(obj) {
    obj.replace(original_texts);
  },

  // try to cut the text in the best way we can
  cut: function(text, delimiter, reset_delimiter) {
    if (escape(text).length < GoogleTranslation.translate_limit) {
      return text;
    }
    var posok = -1;
    var pos1 = text.indexOf(delimiter);
    var l;
    while ((pos1 != -1) && (escape(text.substr(0, pos1)).length <= GoogleTranslation.translate_limit)) {
      posok = pos1;
      pos1 = text.indexOf(delimiter, posok+delimiter.length);
    }
    if (posok != -1) {
      l = delimiter.length;
      if (reset_delimiter) {delimiter = '</p>';}
      return [text.substr(0, posok+l), GoogleTranslation.cut(text.substr(posok+l), delimiter)];
    } else {
      switch(delimiter) {
        case '</p>': return GoogleTranslation.cut(text, '<br />', true);
        case '<br />': return GoogleTranslation.cut(text, '</li>', true);
        case '</li>': return GoogleTranslation.cut(text, '.', true);
        case '.': return GoogleTranslation.cut(text, '>', true);
        case '>': return GoogleTranslation.cut(text, ' ', true);
        default: return [text.substr(0, GoogleTranslation.translate_limit / 2), text.substr(GoogleTranslation.translate_limit / 2)]; // ok, I give up.
                                                                                                                                     // divide limit /2 because of escape()
      }
    }
  },

  handle_email: function(text) {
    return text.replace(new RegExp('\\s*<script[^>]*>[\\s\\S]*?</script>\\s*','ig'),'');
  }
};
