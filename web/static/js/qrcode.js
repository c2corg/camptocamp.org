(function($) {

  $(document).ready(function() {
    $('.license_box').first().prepend('<img class="qrcode printonly" ' +
        'src="https://chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl=' +
        encodeURIComponent($('.mobile_link').first().href + /\d+/.exec(window.location.href)) + '"><br>');
  });

})(jQuery);