(function($) {

  // add the qrcode to the document once dom is loaded
  $(function() {
    $('.license_box').prepend('<img class="qrcode printonly" ' +
        'src="//chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl=' +
        encodeURIComponent('http://' + $('#m-link')[0].host + '/' + /\d+/.exec(window.location.href)) + '"><br>');
  });

})(jQuery);
