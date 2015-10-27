(function($) {

  var COOKIE = "donate";

  $(function() {
    var cookie = readCookie();

    if (!cookie) {
      // no cookie present, display the banner
      // once per session
      alert("display banner");
      setCookie("notnow");
    } else if (cookie === "never") {
      // we're done
      alert("never");
    } else {
      // do not show something yet
      alert("not this session");
    }
  });

  function setCookie(value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      var expires = "; expires=" + date.toGMTString();
    } else {
      var expires = "";
    }
    document.cookie = COOKIE + "=" + value + expires + "; path=/";
  }

  function readCookie() {
    var regex = new RegExp("(?:^|;)\\s?" + COOKIE + "=(.*?)(?:;|$)", "i");
    var match = document.cookie.match(regex);

    return match && unescape(match[1]);
  }
}(jQuery));
