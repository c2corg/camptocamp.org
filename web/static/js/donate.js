(function($) {

  var COOKIE = 'donate';
  var donateDiv = $('#donate');
  var expiration = new Date(2016,2,1); // 01/02/2016

  $(function() {
    var cookie = readCookie();

    if (!cookie && location.pathname != '/donate') {
      // no cookie present, display the banner
      // only once per session
      displayBanner();
    } else {
      // do not show something yet
    }

    // donate buttons if present
    $('.donate-submit').click(notNow);
  });

  function displayBanner() {
    loadBanner();
    donateDiv.show();
    $('.donate-never').click(never);
    $('.donate-notnow').click(notNow);
    $('.donate-change').click(loadBanner);
  }

  function loadBanner() {
    var random = Math.floor(Math.random() * 10);
    $.get('/donate/banner?id=' + random).then(function(data) {
      if (data.url) {
        donateDiv.find('.people').html('<a href="' + data.url + '">' + data.people + '</a>');
      } else {
        donateDiv.find('.people').text(data.people);
      }
      donateDiv.find('.donate-image').get(0).src = '/static/images/donation/' + data.image;
      donateDiv.find('.role').text(data.role);
      donateDiv.find('.presentation').text(data.presentation);
    });
  }

  function notNow() {
    var date = new Date();
    date.setTime(date.getTime()+(2*24*60*60*1000)); // + 2 days
    setCookie("notnow", date);
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

  $(document).ready(function() {
    $('.donate-form').on('submit', function() {
      // ouh que c'est moche
      var val = $('input[name="amount"]').val();
      var i = val.indexOf('.');
      if (i >= 0) {
        val = val.substring(0, i);
      }
      i = val.indexOf(',');
      if (i >= 0) {
        val = val.substring(0, i);
      }
      $('input[name="amount"]').val(val);
    });
  });
}(jQuery));
