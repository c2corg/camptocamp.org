/**
 * This script uses jstorage (https://github.com/andris9/jStorage) in order to regularly
 * save the state of the form on an edit page
 * TODO / TO BE CHECKED
 * - bruno's stuff for mw: should we do sthg?
 * - what happens if we have more than one edit tab at the same time?
 */

if ($.jStorage.storageAvailable()) {
  function simpleObjComp(o1, o2) {
    for (var i in o1) {
      if (o1[i] !== o2[i]) return false;
    }
    return true;
  }

  Event.observe(window, 'load', function() {
    // be sure to flush jstorage when the user clicks on the cancel button
    $$('.action_buttons').each(function(elt) {
      Event.observe(elt.down('li', 2).down(), 'click', function() {
        $.jStorage.flush();
      });
    });

    // check if there is a saved form to load
    if ($F('id') == $.jStorage.get('id') &&
        $F('lang') == $.jStorage.get('lang') &&
        $F('revision') == $.jStorage.get('revision') &&
        $('editform').getAttribute('action').split('/')[1] == $.jStorage.get('module')) {

      // check if there is some difference between the draft and the current form
      var diff = false;
      $('editform').getElements().each(function(e) {
        if (e.tagName.toLowerCase() != 'input' || !(/^(?:file|button|reset|submit)$/i.test(e.type))) {
          var key = e.identify(), cvalue = $F(key), svalue = $.jStorage.get(key);
          
          if ((cvalue != null) &&
              ((typeof cvalue != 'object' && cvalue != svalue) ||
               (typeof cvalue == 'object' && !simpleObjComp(cvalue, svalue)))) {
            diff = true;
          }
        }
      });

      if (diff) {
        // ask before restoring values
        var restore = confirm(i18n_loaddraftform);
      } else {
        // no difference. We don't propose to load the draft, and delete it
        var restore = false;
        $.jStorage.flush();
      }

      if (restore) {
        // restore values from localStorage
        $('editform').getElements().each(function(e) {
          if (e.tagName.toLowerCase() != 'input' || !(/^(?:file|button|reset|submit)$/i.test(e.type))) {
            if (e.tagName.toLowerCase() == 'select') { // selects need special handling. When using jquery, use val()
              for (var j=0; j<e.options.length; j++) {
                e.options[j].selected = false;
              }
              var svalues = $.jStorage.get(e.identify());
              for (var i in svalues) {
                for (j=0; j<e.options.length; j++) {
                  if (svalues[i] == e.options[j].value) {
                    e.options[j].selected = true;
                  }
                }
              }
            } else {
              $(e.identify()).value = $.jStorage.get(e.identify());
            }
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
}
