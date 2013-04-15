(function(C2C) {

  "use strict";

  C2C.submitonce = function(aform) {
    switchFormButtonsStatus(aform, true);
  };

  function switchFormButtonsStatus(aform, disable) {
    if (document.all || document.getElementById){
      var tmp_dom_obj;
      for (var i=0; i < aform.length; i++){        
        tmp_dom_obj = aform.elements[i];
        if ((tmp_dom_obj.type.toLowerCase() == "submit") || (tmp_dom_obj.type.toLowerCase() == "button"))
            tmp_dom_obj.disabled = disable;
      }
    }
  }

  C2C.getWizardRouteRatings = function(div_id) {
    new Ajax.Updater(
      div_id + '_descr', '/routes/getratings', {
        asynchronous:true,
        evalScripts:false,
        onComplete: function(request, json) { Element.hide("indicator"); },
        onFailure: function(request, json) { Element.hide("wizard_" + div_id + "_descr"); },
        onLoading: function(request, json) { Element.show("indicator"); },
        onSuccess: function(request, json){ Element.show("wizard_" + div_id + "_descr"); },
        parameters:"id=" + $(div_id).value
      });
  };

  C2C.remLink = function(link_type, main_id, linked_id, main_doc, strict) {
    if (confirm(confirm_msg))
    {
      var type_linked_id = link_type + '_';
      if (main_doc) {
        type_linked_id = type_linked_id + linked_id;
      } else {
        type_linked_id = type_linked_id + main_id;
      }
      new Ajax.Updater({
          success: type_linked_id,
          failure: 'ajax_feedback_failure'
        },
        '/documents/removeAssociation/main_' + link_type + '_id/' + main_id + '/linked_id/' + linked_id + '/type/' + link_type + '/strict/' + strict, {
          asynchronous: true,
          evalScripts: false,
          method: 'post',
          onComplete: function(request, json) {
            Element.hide('indicator');
            setTimeout('C2C.emptyFeedback("ajax_feedback_failure")', 4000);
          },
          onFailure: function(request, json) {
            Element.show('ajax_feedback_failure');
          },
          onLoading: function(request, json) {
            Element.hide('del_' + type_linked_id);
            Element.show('indicator');
          },
          onSuccess: function(request, json) {
            $$('#'+type_linked_id+', .'+type_linked_id).each(Element.hide);
          }
        });
    }
    return false;
  };

  function showFieldDefault(field, enable) {
    if (field_default[field][2]) {
      var field_id = field_default[field][0];
      if (enable) {
        $(field_id).value = field_default[field][1];
        $(field_id).style.color = 'gray';
      } else {
        $(field_id).value = '';
        $(field_id).style.color = 'black';
      }
    }
  }

  C2C.showAllFieldDefault = function(enable) {
    if (typeof(field_default) != 'undefined') {
      for (var i=0; i < field_default.length; i++) {
        showFieldDefault(i, enable);
      }
    }
  };

  function initFieldDefault() {
    if (typeof(field_default)!='undefined' && typeof(ga_done)!='undefined') {
      var field_id;
      for (var i=0; i < field_default.length; i++) {
        field_id = field_default[i][0];
        if ($(field_id).value == '') {
          field_default[i][2] = true;
          showFieldDefault(i, true);
        } else {
          field_default[i][2] = false;
        }
      }
    }
  }

  function hideFieldDefault(field) {
    if (field_default[field][2]) {
      showFieldDefault(field, false);
      field_default[field][2] = false;
    }
  }

  function hideAllFieldDefault() {
    if (typeof(field_default)!='undefined') {
      for (var i=0; i < field_default.length; i++) {
        hideFieldDefault(i);
      }
    }
  }

  var bbcode_toolbar;
  function initBBcode() {
    bbcode_toolbar = $$('.bbcodetoolcontainer');
    var textbox_list = Array();
    if (bbcode_toolbar.length > 0) {
      bbcode_toolbar.each(function(b) {
        b.setStyle({'visibility': 'hidden'});
        textbox_list.push($w(b.className).last());
      });
        
      textbox_list.each(function(t) {
        $(t).observe('focus', showBBcode);
      });
    }
  }

  function showBBcode() {
    var field_id = this.identify();
    bbcode_toolbar.each(function(b)
    {
      if ($w(b.className).last() == field_id) {
        b.setStyle({'visibility': 'visible'});
      } else {
        b.setStyle({'visibility': 'hidden'});
      }
    });
  }

  document.observe('dom:loaded', function() {
    initBBcode();
    initFieldDefault();
    if ($('editform')) {
      Event.observe('editform', 'submit', hideAllFieldDefault);
    }
  });

})(window.C2C = window.C2C || {});
