(function($) {

  // add the qrcode to the document once dom is loaded
  $(function() {
    $('.license_box').prepend('<img class="qrcode printonly" ' +
        'src="https://chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl=' +
        encodeURIComponent($('#m-link').hostname + '/' + /\d+/.exec(window.location.href)) + '"><br>');
  });

})(jQuery);
