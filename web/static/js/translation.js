var translate_button = '<span class="translate_button" style="display:none">[ <a href="#" onclick="translate(this.parentNode);return false;">'+translate_string+'</a> ]</span>';
var untranslate_button = '<div class="translate_button" style="display:none">[ <a href="#" onclick="untranslate(this.parentNode);return false;">'+untranslate_string+'</a> ]</div>';

var translate_timer = false;

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
  var original_div = obj.next(1);
  var content = original_div.innerHTML;
  google.language.translate(content, language_from, language_to,
    function(result) {
      if (!result.error) {
        var translated_div_content = '<div>'+result.translation+'</div>';
        original_div.hide();
        new Insertion.After(original_div, translated_div_content);
        var translated_div = obj.next(2);
        google.language.getBranding(translated_div);
        new Effect.Highlight(translated_div);
        obj.replace(untranslate_button);
      }
  });
}

function untranslate(obj) {
  var original_div = obj.next(1);
  var translated_div = original_div.next();
  original_div.show();
  translated_div.remove();
  new Effect.Highlight(original_div);
  obj.replace(translate_button);
}

google.load("language", "1", {"callback" : translation_api_loaded});
