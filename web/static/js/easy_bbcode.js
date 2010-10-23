var quote_text = '';
var nickname_postid  = '';

function toggle_spoiler(spoiler)
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
}

function get_quote_text()
{
	var parentNode = null;
	if (window.getSelection)
	{
		quote_text = window.getSelection();
                if (quote_text != '')
		{
			parentNode = $(window.getSelection().anchorNode.parentNode);	
		}
	}
	else if (document.getSelection)
	{
		quote_text = document.getSelection();
		if (quote_text != '')
		{
			parentNode = $(document.getSelection().anchorNode.parentNode);
		}
	}
	else if (document.selection && document.selection.createRange())
	{
		quote_text = document.selection.createRange().text;
		if (quote_text != '')
		{
			parentNode = $(document.selection.createRange().parentElement());
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
}

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

function insert_text(open, close, quote_enable)
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
}

function paste_quote(default_poster)
{
	if (quote_text == '')
	{
            // no text has been selected. Display quote bbcode though
            nickname_postid = default_poster;
	}
	var startq = '[quote=' + nickname_postid + ']\n';
	var endq = quote_text + '\n[/quote]\n';
	insert_text(startq, endq, true);
	quote_text = '';
}

function paste_nick(user_name)
{
	var startq = user_name;
	var endq = '';
	insert_text(startq, endq);
}

function changeTextareaRows(textarea_id, up_down)
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
}
