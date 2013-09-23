// autofocus polyfill

(function($) {
  if (!("autofocus" in document.createElement("input"))) {
    $("input[autofocus]").first().focus();
  }
})(jQuery);
