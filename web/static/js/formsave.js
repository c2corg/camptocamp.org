/**
 * This script uses jstorage (https://github.com/andris9/jStorage) in order to regularly
 * save the state of the form on an edit page
 * TODO / TO BE CHECKED
 * - when the user clicks on Cancel, we should flush jstorage
 * - i18n for confirm string
 * - bruno's stuff for mw: should we do sthg?
 * - what happens if we have more than one edit tab at the same time?
 * - various testing needs to be done
 */

Event.observe(window, 'load', function() {
  // be sure to flush jstorage when the user submits the form
  Event.observe('editform', 'submit', function() {
    $.jStorage.flush();
  });

  // check if there is a saved form to load
  if ($F('id') == $.jStorage.get('id') &&
      $F('lang') == $.jStorage.get('lang') &&
      $F('revision') == $.jStorage.get('revision') &&
      $('editform').getAttribute('action').split('/')[1] == $.jStorage.get('module')) {

    // ask before restoring values
    var restore = confirm('Grab old values?');

    if (restore) {
      // restore values from localStorage
      $('editform').getElements().each(function(e) {
        if (e.tagName.toLowerCase() != 'input' || !(/^(?:file|button|reset|submit)$/i.test(e.type))) {
          $(e.identify()).value = $.jStorage.get(e.identify());
        }
      });
    } else {
      $.jStorage.flush();
    }
  } else { // forms are not the same, erase previous entries
    $.jStorage.flush();
  }

  // periodically save current form
  new PeriodicalExecuter(function() {
    $('editform').getElements().each(function(e) {
      if (e.tagName.toLowerCase() != 'input' || !(/^(?:file|button|reset|submit)$/i.test(e.type))) {
        var key = e.identify();
        if ($F(key) != null) {
          $.jStorage.set(key, $F(key));
        }
      }
    });
    // also save module, useful for new docs
    $.jStorage.set('module', $('editform').getAttribute('action').split('/')[1]);
  }, 20);
});
