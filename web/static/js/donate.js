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
      displayBanner();
    } else {
      // do not show something yet
      console.log("not this session");
    }
  });

  function displayBanner() {
    loadBanner()
    donateDiv.show();
    $('.donate-never').click(never);
    $('.donate-notnow').click(notNow);
    $('.donate-change').click(loadBanner);
  }

  function loadBanner() {
    console.log('load banner');
    var random = Math.floor(Math.random() * 9);
    $.get('/documents/donatebanner?id=' + random).then(function(data) {
      console.log(data);
      if (data.url) {
        donateDiv.find('.people').html('<a href="' + data.url + '">' + data.people + '</a>');
      } else {
        donateDiv.find('.people').text(data.people);
      }
      donateDiv.find('.role').text(data.role);
      donateDiv.find('.presentation').text(data.presentation);
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
