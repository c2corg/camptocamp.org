// Forum categories folding
(function($) {

  var pref = [];
  var cookie_name = 'punbb_dyncat';
  var date = new Date();
  date.setFullYear(date.getFullYear() + 1);

  // save preference in cookie (1 year expiration time)
  function saveCookie(value) {
    document.cookie = cookie_name + '=' + value +
      '; expires=' + date.toGMTString();
  }

  // save preference on server
  function savePref(value) {
    if ($('#name_to_use').length) { // logged user
      $.post('/users/savepref', { 'name': cookie_name, 'value': value });
    }
  }

  $(function (save_on_server) {

    if ($('#punindex').length) {
      var re = new RegExp('(?:^|;)\\s?' + cookie_name + '=([01_]+)(?:;|$)','i');
      var cookie_value = re.exec(document.cookie);
      var pref_save = cookie_value ? cookie_value[1].split('_') : [];

      // go through categories
      $('h2').parent('.blocktable').each(function(i) {
        pref[i] = (pref_save[i]) ? parseInt(pref_save[i], 10) : 1;

        // add picto
        var h2 = $(this).children(':first');
        var dh = h2.text();
        h2.html('<span class="picto picto_' + (!!pref[i] ? 'close' : 'open') +
          '"></span> <span>' + dh + '</span>');
        $(this).find('table:first').toggle(!!pref[i]);

        h2.click(function() {
          // toggle forum category
          $(this).children('span').first()
           .toggleClass('picto_close picto_open');

          var table = $(this).next().find('table:first');
          table.toggle();
          if (table.is(':visible')) {
            pref[i] = 1;
          } else {
            pref[i] = 0;
          }

          // save preferences
          pref_save = pref.join('_');
          saveCookie(pref_save);
          savePref(pref_save);
        });
      });
    }
  });

})(jQuery);
