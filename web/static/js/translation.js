var translate_button = '<span class="translate_button" style="display:none">[ <a href="#" onclick="translate(this.parentNode);return false;">'+translate_params[0]+'</a> ]</span>';
var untranslate_button = '<span class="translate_button" style="display:none">[ <a href="#" onclick="untranslate(this.parentNode);return false;">'+translate_params[1]+'</a> ]</span>';
var translate_wait = '<span class="translate_wait translate_button">'+translate_params[2]+'</span>';
var language_from = translate_params[3];
var language_to = translate_params[4];


var conc_strings;
var conc_translated;
var conc_total;

var translate_limit = 1000;

function translation_api_loaded() {
  if (!google.language.isTranslatable(language_from)
      || !google.language.isTranslatable(language_to)) {
    return;
  }
  $$('.translatable').each(function(o) {
    new Insertion.Top(o, translate_button);
    o.observe('mouseover', function(e) {
        this.down().show();
    });
    o.observe('mouseout', function(e) {
        this.down().hide();
    });
  });
}

function translate(obj) {
  var original_div = obj.next('div.field_value');
  var content = handle_email(original_div.innerHTML);
  obj.replace(translate_wait);
  obj = original_div.previous('span.translate_button');
  if (content.length < translate_limit) {
    google.language.translate(content, language_from, language_to,
      function(result) {
        if (!result.error) {
          show_translation(obj, result.translation);
        } // TODO ERROR
    });
  } else { // text is too long
    var strings = cut(content, '</p>').flatten();
    conc_total = strings.length;
    conc_translated = 0;
    conc_strings = new Array();
    strings.each(function(s, index) {
        google.language.translate(s, language_from, language_to,
          function(result) {
            if (!result.error) {
              add_partial_translation(obj, result.translation, index);
            } // TODO ERROR
          });
    });
  }
}

function show_translation(obj, translated_text) {
  var translated_div_content = '<div>'+translated_text+'</div>';
  var original_div = obj.next('div.field_value');
  original_div.hide();
  new Insertion.After(original_div, translated_div_content);
  var translated_div = original_div.next();
  google.language.getBranding(translated_div);
  new Effect.Highlight(translated_div);
  obj.replace(untranslate_button);
}

function add_partial_translation(obj, translated_part, index) {
  conc_translated++;
  conc_strings[index] = translated_part;
  if (conc_translated == conc_total) {
    var translated_text = '';
    for (var i = 0, len = conc_strings.length; i < len; ++i) {
      translated_text += conc_strings[i];
    }
    show_translation(obj, translated_text);
  }
}

function untranslate(obj) {
  var original_div = obj.next('div.field_value');
  var translated_div = original_div.next();
  original_div.show();
  translated_div.remove();
  new Effect.Highlight(original_div);
  obj.replace(translate_button);
}

// try to cut the text in the best way we can
function cut(text, delimiter, reset_delimiter) {
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
    return [text.substr(0, posok+delimiter.length), cut(text.substr(posok+delimiter.length), delimiter)];
  } else {
    switch(delimiter) {
      case '</p>': return cut(text, '<br />', true);
      case '<br />': return cut(text, '</li>', true);
      case '</li>': return cut(text, '.', true);
      case '.': return cut(text, '>', true);
      case '>': return cut(text, ' ', true);
      case ' ':
      default: return [text.substr(0, translate_limit), text.substr(translate_limit)]; // ok, I give up
    }
  }
}

function handle_email(text) {
    return text.replace(new RegExp('\\s*<script[^>]*>[\\s\\S]*?</script>\\s*','ig'),'');
}

google.load("language", "1", {"callback" : translation_api_loaded});
