// bbcode for forums
(function(C2C) {

"use strict";

var quote_text = '';
var nickname_postid  = '';

function storeCaret(textEl)
{
        if (textEl.createTextRange)
        {
                textEl.caretPos = document.selection.createRange().duplicate();
        }
}

function getCaretPosition(txtarea)
{
        var caretPos = {};

        // simple Gecko/Opera way
        if(txtarea.selectionStart || txtarea.selectionStart === 0)
        {
                caretPos.start = txtarea.selectionStart;
                caretPos.end = txtarea.selectionEnd;
        }
        // dirty and slow IE way
        else if(document.selection)
        {
                // get current selection
                var range = document.selection.createRange();

                // a new selection of the whole textarea
                var range_all = document.body.createTextRange();
                range_all.moveToElementText(txtarea);

                // calculate selection start point by moving beginning of range_all to beginning of range
                var sel_start;
                for (sel_start = 0; range_all.compareEndPoints('StartToStart', range) < 0; sel_start++)
                {
                        range_all.moveStart('character', 1);
                }

                txtarea.sel_start = sel_start;

                // we ignore the end value for IE, this is already dirty enough and we don't need it
                caretPos.start = txtarea.sel_start;
                caretPos.end = txtarea.sel_start;
        }

        return caretPos;
}

C2C.toggle_spoiler = function(spoiler)
{
	var text_box=spoiler.getElementsByTagName('div')[0];
	if (text_box.style.visibility != 'hidden')
	{
		text_box.style.visibility = 'hidden';
		text_box.style.display='none';
		text_box.style.height='0';
	}
	else
	{
		text_box.style.visibility='';
		text_box.style.display='block';
		text_box.style.height='';
	}
};


// try to correct a bit the selected text so that the result is more readable
// especially with regards to links
// (e.g. shorten http://www.my ... link were badly pasted)
// It will work most of the time, but will fail in many (but rare) situations
// TODO We could be more clever and translate the html to bbcode. This would allow
// a better text pasting (images, font settings, ...),
// but wiould also be more complicated hardly maintainable
function correctLinks(text, html)
{
        var links = $(html).getElementsByTagName('a');
        for (var i = 0, length = links.length; i < length; i++) {
                var title = links[i].childNodes[0].nodeValue;
                var href = links[i].getAttribute('href');
                if (/https?:\/\//.test(title)) {
			text = text.replace(links[i].childNodes[0].nodeValue, href);
                }
		else
		{
			text = text.replace(title, '[url=' + href + ']' + title + '[/url]');
		}
	}
	return text;
}

C2C.get_quote_text = function()
{
	var parentNode = null;
        var quote;
        var text;
        var html;
	if (window.getSelection)
	{
		quote = window.getSelection();
                if (quote.rangeCount)
		{
			parentNode = $(quote.anchorNode.parentNode);
			text = quote.toString();
			// in case of multiple selection, we keep only the first one
			html = document.createElement('div');
			html.appendChild(quote.getRangeAt(0).cloneContents());
		}
	}
	else if (document.selection && document.selection.type == 'Text')
	{
		quote = document.selection.createRange();
		if (quote.text != '')
		{
			parentNode = $(quote.parentElement());
			text = quote.text;
			html = document.createElement('div');
			html.innerHTML = quote.htmlText;
		}
	}

	if (parentNode)
	{
		var blockpost = parentNode.up('.postright');
		if (!blockpost)
		{
			// not quoting any post
			return;
		}
		else
		{
                        quote_text = correctLinks(text, html);

			// retrieve poster nickname and post id (different whether on post.php or viewtopic.php and invited user)
			var nickname = blockpost.previous('.postleft').down('strong');
			if (nickname.down('a'))
			{
				nickname = nickname.down('a').innerHTML;
			}
			else
			{
				nickname = nickname.innerHTML;
			}
			if (nickname.indexOf('[') != -1 || nickname.indexOf(']') != -1)
			{
				if (nickname.indexOf('"') == -1)
				{
					nickname = '"'+nickname+'"';
				}
				else
				{
					nickname = "'"+nickname+"'";
				}
			}

                        var postid = parseInt(blockpost.up('.inbox').id.substring(1), 10);
                        if (isNaN(postid))
                        {
                            postid = blockpost.up('.blockpost').id.substring(1);
                        }
                        
                        nickname_postid = nickname + '|' + postid;
		}
	}
};

C2C.insert_text = function(open, close, quote_enable)
{
	var msgfield = (document.all) ? document.all.req_message : (document.forms['post'] ? document.forms['post']['req_message'] : document.forms['edit']['req_message']);

	var st = msgfield.scrollTop;
	msgfield.focus();
	
	if (document.selection && document.selection.createRange)
	{
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
	
	if ((quote_enable && quote_text.length > 0) || (msgfield.selectionEnd && (msgfield.selectionEnd - msgfield.selectionStart > 0)))
	{
		if ((open.substring(0,4) == '[url') || (open.substring(0,6) == '[email'))
		{
			newPos -= 1;
		}
		else
		{
			newPos = endPos + open.length + close.length;
		}
	}
	
	msgfield.value = startText + open + selText + close + endText;
	
	if (!isNaN(msgfield.selectionStart))
	{
		msgfield.selectionStart = newPos;
		msgfield.selectionEnd = newPos;
	}	
	else if (document.selection)
	{
		var range = msgfield.createTextRange(); 
		range.move("character", newPos); 
		range.select();
		storeCaret(msgfield);
	}

	msgfield.focus();
	msgfield.scrollTop = st;
	msgfield.focus();
	return;
};

C2C.paste_quote = function(default_poster)
{
	if (quote_text == '')
	{
            // no text has been selected. Display quote bbcode though
            nickname_postid = default_poster;
	}
	var startq = '[quote=' + nickname_postid + ']\n';
	var endq = quote_text + '\n[/quote]\n';
	C2C.insert_text(startq, endq, true);
	quote_text = '';
};

C2C.paste_nick = function(user_name)
{
	var startq = user_name;
	var endq = '';
	C2C.insert_text(startq, endq);
};

C2C.changeTextareaRows = function(textarea_id, up_down)
{
    var rows = $(textarea_id).rows;
    if(up_down)
    {
        rows += 5; 
    }
    else
    {
        rows = Math.max(5, rows - 5);
    }
    $(textarea_id).rows = rows;
};


})(window.C2C = window.C2C || {});
