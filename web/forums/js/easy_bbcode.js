quote_text = '';

function toggle_spoiler(spoiler)
{
	text_box=spoiler.getElementsByTagName('p')[0];
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
	if (document.selection && document.selection.createRange())
	{
		quote_text = document.selection.createRange().text;
	}
	if (document.getSelection)
	{
		quote_text = document.getSelection();
	}
}

function paste_quote(user_name)
{
	startq = '[quote=' + user_name + ']\n';
	endq = quote_text + '\n[/quote]\n';
	insert_text(startq, endq, true);
	quote_text = '';
}

function paste_nick(user_name)
{
	startq = user_name;
	endq = '';
	insert_text(startq, endq);
}

function insert_text(open, close, quote_enable)
{
	msgfield = (document.all) ? document.all.req_message : (document.forms['post'] ? document.forms['post']['req_message'] : document.forms['edit']['req_message']);

	var st = msgfield.scrollTop;
	msgfield.focus();
	
	if (document.selection && document.selection.createRange)
	{
		sel = document.selection.createRange();
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
		if (open.substring(0,4) == '[url')
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

function caretPosition()
{
	var start = null;
	var end = null;
}

function getCaretPosition(txtarea)
{
	var caretPos = new caretPosition();
	
	// simple Gecko/Opera way
	if(txtarea.selectionStart || txtarea.selectionStart == 0)
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

function storeCaret(textEl)
{
	if (textEl.createTextRange)
	{
		textEl.caretPos = document.selection.createRange().duplicate();
	}
}
