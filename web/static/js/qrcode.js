(function() {

  "use strict";

  Event.observe(window, 'load', function()
  {
    if ($$('.license_box')) {
      $$('.license_box').first().insert({ top: '<img class="qrcode printonly" src="https://chart.googleapis.com/chart?chs=70x70&amp;cht=qr&amp;choe=UTF-8&amp;chl='
                                               + encodeURIComponent($$('.mobile_link').first().href + /\d+/.exec(window.location.href))
                                               + '"><br />' });
    } // TODO
  });

})();
