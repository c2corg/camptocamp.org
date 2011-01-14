Event.observe(window, 'load', function()
{
  if ($('footer')) {
    $('footer').insert({ after: new Element('img',
                                { 'class':'qrcode printonly',
                                  src: 'https://chart.googleapis.com/chart?chs=120x120&cht=qr&choe=UTF-8&chl='
                                       + encodeURIComponent(window.location.href) }) });
  }
});
