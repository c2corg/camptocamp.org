(function($, l) {

  // add the qrcode to the document once dom is loaded
  $(function() {
    $('.license_box').prepend('<img class="qrcode printonly" ' +
        'src="//chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl=' +
        encodeURIComponent(l.protocol + '//' + l.host + '/' + /\d+/.exec(l.href)) + '"><br>');
  });

})(jQuery, window.location);
