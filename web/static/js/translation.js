var translate_wait = '<span class="translate_wait translate_button">'
                   + translate_params[2] + '</span>';
var language_from = translate_params[3];
var language_to = translate_params[4];

var conc_strings;
var conc_translated;
var conc_total;

var original_texts = null;

var translate_limit = 1000;

var translate_button = '<span class="translate_button"><a href="#" '
                     + 'onclick="GoogleTranslation.translate_all();return false;">'
                     + translate_params[0] + '</a></span>';

var untranslate_button = '<span class="translate_button"><a href="#" '
                       + 'onclick="GoogleTranslation.untranslate_all();return false;">'
                       + translate_params[1] + '</a></span>';

GoogleTranslation = {

  translation_api_loaded: function() {
    if (!google.language.isTranslatable(language_from)
        || !google.language.isTranslatable(language_to)) {
      return;
    }
    $$('.switch_lang').each(function(o) {
      new Insertion.Bottom(o, translate_button);
    });
  },

  translate_all: function() {
    $$('.translate_button').invoke('replace', translate_wait);
    $$('.section_subtitle, .toc_link').invoke('addClassName', 'notranslate');

    GoogleTranslation.translate($$('.article_contenu')[0]);
  },

  untranslate_all: function() {
    GoogleTranslation.untranslate($$('.article_contenu')[0]);
    $$('.translate_button').invoke('replace', translate_button);
    $$('.gBranding').invoke('remove');
  },

  translate: function(obj) {
    var content = GoogleTranslation.handle_email(obj.innerHTML);
    if (content.length < translate_limit) {
      google.language.translate(content, language_from, language_to,
        function(result) {
          if (!result.error) {
            GoogleTranslation.show_translation(obj, result.translation);
          } // TODO ERROR
      });
    } else { // text is too long
      var strings = GoogleTranslation.cut(content, '</p>').flatten();
      conc_total = strings.length;
      conc_translated = 0;
      conc_strings = new Array();
      strings.each(function(s, index) {
        google.language.translate(s, language_from, language_to,
          function(result) {
            if (!result.error) {
              GoogleTranslation.add_partial_translation(obj, result.translation, index);
            } // TODO ERROR
          });
      });
    }
  },

  show_translation: function(obj, translated_text) {
    var translated_div_content = '<div class="article_contenu">'+translated_text+'</div>';
    var original_div = obj;
    new Insertion.After(original_div, translated_div_content);
    var translated_div = original_div.next('.article_contenu');
    original_texts = original_div;
    original_div.remove();

    $$('.translatable').each(function(o) {
      google.language.getBranding(o);
    });

    new Effect.Highlight('description_section_container');

    $$('.translate_button').invoke('replace', untranslate_button);
  },

  add_partial_translation: function(obj, translated_part, index) {
    conc_translated++;
    conc_strings[index] = translated_part;
    if (conc_translated == conc_total) {
      var translated_text = '';
      for (var i = 0, len = conc_strings.length; i < len; ++i) {
        translated_text += conc_strings[i];
      }
      GoogleTranslation.show_translation(obj, translated_text);
    }
  },

  untranslate: function(obj) {
    obj.replace(original_texts);
  },

  // try to cut the text in the best way we can
  cut: function(text, delimiter, reset_delimiter) {
    if (text.length < translate_limit) {
      return text;
    }
    var posok = -1;
    var pos1 = text.indexOf(delimiter);
    while ((pos1 != -1) && (pos1 <= translate_limit)) {
      posok = pos1;
      pos1 = text.indexOf(delimiter, posok+delimiter.length);
    }
    if (posok != -1) {
      if (reset_delimiter) {delimiter = '</p>';}
      return [text.substr(0, posok+delimiter.length), GoogleTranslation.cut(text.substr(posok+delimiter.length), delimiter)];
    } else {
      switch(delimiter) {
        case '</p>': return GoogleTranslation.cut(text, '<br />', true);
        case '<br />': return GoogleTranslation.cut(text, '</li>', true);
        case '</li>': return GoogleTranslation.cut(text, '.', true);
        case '.': return GoogleTranslation.cut(text, '>', true);
        case '>': return GoogleTranslation.cut(text, ' ', true);
        case ' ':
        default: return [text.substr(0, translate_limit), text.substr(translate_limit)]; // ok, I give up
      }
    }
  },

  handle_email: function(text) {
    return text.replace(new RegExp('\\s*<script[^>]*>[\\s\\S]*?</script>\\s*','ig'),'');
  }
}

google.load("language", "1", {"callback" : GoogleTranslation.translation_api_loaded});
