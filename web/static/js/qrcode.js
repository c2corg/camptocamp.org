(function($) {

  $(document).ready(function() {
    var div = $('.license_box').first();
    if (div) {
      div.prepend('<img class="qrcode printonly" src="https://chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl=' +
                  encodeURIComponent($('.mobile_link').first().href + /\d+/.exec(window.location.href)) + '"><br>');
    }
  });

})(jQuery);
