Event.observe(window, 'load', function()
{
  if ($('footer')) {
    $('footer').insert({ after: new Element('img',
                                { 'class':'qrcode printonly',
                                  src: 'https://chart.googleapis.com/chart?chs=70x70&cht=qr&choe=UTF-8&chl='
                                       + encodeURIComponent($$('.mobile_link').first().href + /\d+/.exec(window.location.href)) }) });
  }
});
