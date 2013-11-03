// enable mailcheck.js on loginform
// this should help prevent user wrongly entering their email
(function($) {
  var email_input = $('#email'),
      email_suggestion = $('.email-suggestion'),
      suggested_email = $('.suggested-email');

  // customize our domains
  // we include the most used email providers based on a snapshot of the db
  var domains = [ "gmail.com", "hotmail.com", "yahoo.fr", "wanadoo.fr", "hotmail.fr", "free.fr",
                  "orange.fr", "bluewin.ch", "laposte.net", "aol.com", "voila.fr", "yahoo.com",
                  "yahoo.com", "club-internet.fr", "neuf.fr", "caramail.com", "libero.it",
                  "libertysurf.fr", "live.fr", "msn.com", "sfr.fr", "tiscali.fr", "yahoo.it",
                  "alice.it", "hotmail.it", "yahoo.es", "netplus.ch", "dbmail.com", "sunrise.ch",
                  "freesurf.ch", "tin.it", "cegetel.net", "gmx.de", "gmx.ch", "bluemail.ch",
  // and other common ones
                  "me.com", "mac.com", "live.com", "googlemail.com", "facebook.com", "gmx.com",
                  "mail.com", "outlook.com", "verizon.net" ];
  var tlds = [ "co.uk", "com", "net", "org", "info", "edu", "gov", "fr", "it", "de", "es", "cat", "eus" ] 

  // check email value on blur
  email_input.on('blur', function() {
    $(this).mailcheck({
      domains: domains,
      topLevelDomains: tlds,
      suggested: function(element, suggestion) {
        suggested_email.text(suggestion.full);
        email_suggestion.show();
      },
      empty: function(element) {
        email_suggestion.hide();
      }
    });
  });

  // link beahviour
  suggested_email.click(function() {
    email_input.val($(this).text());
    email_suggestion.hide();
  });
})(jQuery);
