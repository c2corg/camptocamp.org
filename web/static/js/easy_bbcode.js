// bbcode for forums
(function(C2C, $) {

  var quote_text = '';
  var nickname_postid  = '';

  function getCaretPosition(txtarea) {

    var caretPos = {};

    // standard way
    if ('selectionStart' in txtarea) {
      caretPos.start = txtarea.selectionStart;
      caretPos.end = txtarea.selectionEnd;
    }
    // old ie
    else if (document.selection) {

      // get current selection
      var range = document.selection.createRange();

      // a new selection of the whole textarea
      var range_all = document.body.createTextRange();
      range_all.moveToElementText(txtarea);

      // calculate selection start point by moving beginning of range_all to beginning of range
      for (var sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++) {
        range_all.moveStart('character', 1);
      }

      txtarea.sel_start = sel_start;

      // we ignore the end value for IE, this is already dirty enough and we don't need it
      caretPos.start = txtarea.sel_start;
    }

    return caretPos;
  }

  C2C.toggle_spoiler = function(spoiler) {
    $(spoiler).find('div').first().toggle();
  };


  // try to correct a bit the selected text so that the result is more readable
  // especially with regards to links
  // (e.g. shorten http://www.my ... link were badly pasted)
  // It will work most of the time, but will fail in many (but rare) situations
  // TODO We could be more clever and translate the html to bbcode. This would allow
  // a better text pasting (images, font settings, ...),
  // but wiould also be more complicated hardly maintainable
  function correctLinks(text, html) {
    var links = $(html).find('a').each(function() {
        var title = $(this).text();
        var href = this.href;
        if (/https?:\/\//.test(title)) {
          text = text.replace(title, href);
        } else {
          text = text.replace(title, '[url=' + href + ']' + title + '[/url]');
       }
    });
    return text;
  }

  C2C.get_quote_text = function() {
    var parentNode = null;
    var quote, text, html;

    if (window.getSelection) { // modern browser
      quote = window.getSelection();
      if (quote.rangeCount) {
        parentNode = quote.anchorNode.parentNode;
        text = quote.toString();
        // in case of multiple selection, we keep only the first one
        html = document.createElement('div');
        html.appendChild(quote.getRangeAt(0).cloneContents());
      }
    } else if (document.selection && document.selection.type == 'Text') { // old ie
      quote = document.selection.createRange();
      if (quote.text !== '') {
        parentNode = quote.parentElement();
        text = quote.text;
        html = document.createElement('div');
        html.innerHTML = quote.htmlText;
      }
    }

    if (parentNode) {

      // find related quote
      var blockpost = $(parentNode).parents('.postright');
      if (!blockpost.length) {
        // not quoting any post
        return;
      }  else {
        quote_text = correctLinks(text, html);

        // retrieve poster nickname and post id (different whether on post.php or viewtopic.php and invited user)
        var nickname = blockpost.prevAll('.postleft:first strong:first').text();
        if (nickname.indexOf('[') != -1 || nickname.indexOf(']') != -1) {
          if (nickname.indexOf('"') == -1) {
            nickname = '"' + nickname + '"';
          } else {
            nickname = "'"+nickname+"'";
          }
        } 

        // retrieve post id
        var postid = parseInt(blockpost.parents('.inbox:first').attr('id').substring(1), 10);
        if (isNaN(postid)) {
          postid = blockpost.find('.blockpost:first').attr('id').substring(1);
        }
                        
        nickname_postid = nickname + '|' + postid;
      }
    }
  };

  // insert bbcode
  C2C.insert_text = function(open, close, quote_enable) {

    var msgfield = $('#req_message')[0];

    var st = msgfield.scrollTop;
    msgfield.focus();
	
    if (document.selection && document.selection.createRange) {
      var sel = document.selection.createRange();
      sel.text = open + sel.text + close;
      msgfield.scrollTop = st;
      msgfield.focus();
      return;
    }
	
    var textLength = msgfield.value.length;
    var startPos = msgfield.selectionStart;
    var endPos = msgfield.selectionEnd;
    var startText = msgfield.value.substring(0, startPos);
    var selText = msgfield.value.substring(startPos, endPos);
    var endText = msgfield.value.substring(endPos, textLength);
    var newPos = getCaretPosition(msgfield).start + open.length;
	
    if ((quote_enable && quote_text.length > 0) ||
         (msgfield.selectionEnd && (msgfield.selectionEnd - msgfield.selectionStart > 0))) {

      if ((open.substring(0,4) == '[url') || (open.substring(0,6) == '[email')) {
        newPos -= 1;
      } else {
        newPos = endPos + open.length + close.length;
      }
    }
	
    msgfield.value = startText + open + selText + close + endText;
	
    if (!isNaN(msgfield.selectionStart)) {
      msgfield.selectionStart = newPos;
      msgfield.selectionEnd = newPos;
    } else if (document.selection) {
      var range = msgfield.createTextRange();
      range.move("character", newPos); 
      range.select();
      msgfield.caretPos = document.selection.createRange().duplicate();
    }

    msgfield.focus();
    msgfield.scrollTop = st;
    msgfield.focus();
    return;
  };

  C2C.paste_quote = function(default_poster) {

    if (quote_text === '') {
      // no text has been selected. Display quote bbcode though
      nickname_postid = default_poster;
    }

    var startq = '[quote=' + nickname_postid + ']\n';
    var endq = quote_text + '\n[/quote]\n';
    C2C.insert_text(startq, endq, true);
    quote_text = '';
  };

  C2C.paste_nick = function(user_name) {
    var startq = user_name;
    var endq = '';
    C2C.insert_text(startq, endq);
  };

  C2C.changeTextareaRows = function(id, up_down) {
    var textarea = $('textarea#' + id);
    var rows = parseInt(textarea.attr('rows'), 10);
    if (up_down) {
      rows += 5; 
    } else {
      rows = Math.max(5, rows - 5);
    }

    textarea.attr('rows', rows);
  };


})(window.C2C = window.C2C || {}, jQuery);
