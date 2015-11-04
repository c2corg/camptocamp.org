(function($) {

  var COOKIE = 'donate';
  var donateDiv = $('#donate');
  var expiration = new Date(2016,2,1); // 01/02/2016

  $(function() {
    console.log('plop');
    var cookie = readCookie();

    if (!cookie) {
      // no cookie present, display the banner
      // only once per session
      console.log("display banner");
      loadBanner();
      displayBanner();
    } else {
      // do not show something yet
      console.log("not this session");
    }
  });

  function displayBanner() {
    donateDiv.show();
    $('.donate-never').click(never);
    $('.donate-notnow').click(notNow);
    loadBanner();
  }

  function loadBanner() {
    console.log('load banner');
    var random = Math.floor(Math.random() * 9);
    var banner;
    switch (random) {
      case 0: banner = 'stern'; break;
      case 1: banner = 'soty'; break;
      case 2: banner = 'bach'; break;
      case 3: banner = 'jonglez'; break;
      case 4: banner = 'meignan'; break;
      case 5: banner = 'jaillet'; break;
      case 6: banner = 'sansov'; break;
      case 7: banner = 'gabarrou'; break;
      case 8: banner = 'vuilleumier'; break;
    }

    $.get('/banner?people=' + banner).then(function(data) {
      console.log(data);
    });
  }

  function notNow() {
    setCookie("notnow");
    donateDiv.remove();
  }

  function never() {
    setCookie("never", expiration)
    donateDiv.remove();
  }

  function setCookie(value, date) {
    if (date) {
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
