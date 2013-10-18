// bbcode for the topoguide
(function(C2C, $) {

  var img_tag;

  // apply bbcode button
  // this is more complex than for the forums,
  // because we more complex bbcodes
  C2C.insertBbcode = function(selec, targetElm) {
    var opening_tag, closing_tag, selec_2;

    switch (selec) {
      case 'wl': // wiki link
        opening_tag = '[[|';
        closing_tag = ']]';
        selec_2 = '';
        break;
      case 'L#': // route line
        opening_tag = 'L# | ';
        closing_tag = ' |  | ';
        selec_2 = 'L# ';
        break;
      case 'url':
        opening_tag = '[url=]';
        closing_tag = '[/url]';
        selec_2 = selec;
        break;
      case 'img':
        opening_tag = '';
        closing_tag = img_tag;
        selec_2 = selec;
        break;
      default:
        opening_tag = '[' + selec + ']';
        closing_tag = '[/' + selec + ']';
        selec_2 = selec;
        break;
    }

    var e = $('#' + targetElm)[0];
    var value = e.value;

    var scrollPos = e.scrollTop;

    if ('selectionStart' in e) {

      var start = e.selectionStart;
      var end = e.selectionEnd;

      // compute new value
      e.value = value.substring(0, start) +
                opening_tag +
                value.substring(start, end) +
                closing_tag +
                value.substring(end, e.textLength);

      // be sure to put focus back
      e.scrollTop = scrollPos;
      e.focus();

      // put cursor at the end of the added bbcode caret
      var newPos;
      if (selec == 'wl' || selec == 'url' || start == end) {
        newPos = start + selec_2.length + 2;
      } else {
        newPos = end + opening_tag.length + closing_tag.length;
      }
      e.setSelectionRange(newPos, newPos);

    } else if (document.selection) { // old ie

      // check that selected text is from the textarea
      var range = document.selection.createRange();
      if (range && range.parentElement() == e && range.text.length) {
          range.text = opening_tag + range.text + closing_tag;
          range.select();
      } else {
        // enjoy
        e.focus(e.caretPos);
        e.focus(e.value.length);
        e.caretPos = document.selection.createRange().duplicate();

        var magic = "%~%";
        var orig = e.value;
        e.caretPos.text = magic;
        var i = e.value.search(magic);
        e.value = orig.substr(0, i) + opening_tag + closing_tag + orig.substr(i, e.value.length);

        var r = 0;
        for (var n = 0; n < i; n++) {
          if (/[\r]/ig.test(e.value.substr(n, 2))) {
            r++;
          }
        }

        var pos = i + 2 + selec_2.length - r;
        r = e.createTextRange();
        r.moveStart('character', pos);
        r.collapse();
        r.select();
      }
      e.focus();
    }
  };

  // expand or contract textarea height
  C2C.changeTextareaSize = function(id, up_down) {
    var textarea = $('textarea#' + id);
    var height = textarea.height();

    textarea.height(!!up_down ? height + 80 : Math.max(60, height - 80));
  };

  // image stuff

  // if custom legend option is unchecked, be sure to
  // update corresponding field value with original legend
  C2C.doUpdateImageLegend = function() {
    var custom = $('#customlegend').is(':checked');
    if (custom) {
      $('#legend').removeAttr('disabled');
    } else {
      $('#legend').attr('disabled', 'disabled')
                  .val($('.selected_image img').first().attr('alt'));
    }
  };

  // when a new image is selected in the list
  C2C.updateSelectedImage = function(img) {
    // update selected image
    var selected_class = 'selected_image';
    $('div.image').removeClass(selected_class);
    var selected = $(img).parent().parent().addClass(selected_class);

    // update legend
    C2C.doUpdateImageLegend();

    // update for id
    $('#id').value = selected.attr('id').substring(17);
  };

  // finally insert tag in textarea
  C2C.doInsertImgTag = function() {
    var align = $('input[name=alignment]:checked').last().val() || '';
    var borderlegend = $('#hideborderlegend').is(':checked') ? ' no_border no_legend' : '';

    // build tag
    img_tag = '[img=' + $('#id').val() + ' ' + align + borderlegend;
    if ($('#customlegend').is(':checked')) {
      img_tag += ']' + $('#legend').val() + '[/img]';
    } else {
      img_tag += '/]';
    }
    img_tag += "\n";

    C2C.insertBbcode('img', $('#div').val());
    $.modalbox.hide();
  };

  // bbcode toolbar, initiated once dom loaded
  $(function() {
    var bbcode_toolbars = $('.bbcodetoolcontainer');
    var textareas = bbcode_toolbars.next();

    // hide all bbcode toolbars on start
    bbcode_toolbars.css('visibility', 'hidden');

    // hide all toolbars except the one related to the textarea on  focus
    bbcode_toolbars.next().focus(function() {
      bbcode_toolbars.css('visibility', 'hidden');
      $(this).prev().css('visibility', 'visible');
    });
  });

})(window.C2C = window.C2C || {}, jQuery);
