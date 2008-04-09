quote_text = '';

function insert_text(open, close) {
	msgfield = (document.all) ? document.all.req_message : (document.forms['post'] ? document.forms['post']['req_message'] : document.forms['edit']['req_message']);
	if (document.selection && document.selection.createRange) {
		msgfield.focus();
		sel = document.selection.createRange();
		sel.text = open + sel.text + close;
		msgfield.focus();
	}
	else if (msgfield.selectionStart || msgfield.selectionStart == '0') {
		var scrollPos = msgfield.scrollTop;
		var startPos = msgfield.selectionStart;
		var endPos = msgfield.selectionEnd;

		msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
		msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.scrollTop = scrollPos;
		msgfield.focus();
	}
	else {
		msgfield.value += open + close;
		msgfield.focus();
	}
	return;
}

function get_quote_text() {
	if (document.selection && document.selection.createRange()) {
		quote_text = document.selection.createRange().text;
	}
	if (document.getSelection) {
		quote_text = document.getSelection();
	}
}

function paste_quote(user_name) {
	startq = '[quote=' + user_name + ']\n';
	endq = quote_text + '\n[/quote]\n';
	insert_text(startq,endq);
}

function paste_nick(user_name) {
	startq = user_name;
	endq = '';
	insert_text(startq,endq);
}
