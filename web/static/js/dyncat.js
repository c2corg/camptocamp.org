/* Contribution prise sur http://www.actulab.com/les-cookies-en-javascript.php */
var date = new Date();
date.setFullYear(date.getFullYear()+1);

var pref = [];

function EcrireCookie(nom, valeur)
{
	var argv=arguments;
	var argc=arguments.length;
	var expires=(argc > 2) ? argv[2] : null;
	var path=(argc > 3) ? argv[3] : null; // TODO use it
	var domain=(argc > 4) ? argv[4] : null;
	var secure=(argc > 5) ? argv[5] : false;
	document.cookie=nom+"="+escape(valeur)+
	((expires===null) ? "" : ("; expires="+expires.toGMTString()))+
	((domain===null) ? "" : ("; domain="+domain))+
	((secure===true) ? "; secure" : "");
}

function getCookieVal(offset)
{
	var endstr=document.cookie.indexOf (";", offset);
	if (endstr==-1) {
		endstr=document.cookie.length;
	}
	return unescape(document.cookie.substring(offset, endstr));
}

function LireCookie(nom)
{
	var arg=nom+"=";
	var alen=arg.length;
	var clen=document.cookie.length;
	var i=0;
	while (i<clen)
	{
		var j=i+alen;
		if (document.cookie.substring(i, j)==arg) { return getCookieVal(j); }
		i=document.cookie.indexOf(" ",i)+1;
		if (i===0) { break; }
	}
	return null;
}

function dyncat(h, t) {
	var h2 = $$("h2")[h];
	var table = $$("table")[t];
	
	table.toggle();
	h2.down("span").toggleClassName('picto_close');
	h2.down("span").toggleClassName('picto_open');
	if (table.visible()) {
		pref[t] = 1;
	} else {
		pref[t] = 0;
	}
}

function ArrayIdx() {
	var category = [];	
	for (var i = 1; document.getElementById("idx"+i) !== null; i++)
	{
	if( document.getElementById("idx"+i) !== null)
		{
			category[""+i] = document.getElementById("idx"+i);
		}
	}
	category.shift();
	return category.length;
}

function SavePref(name, value) {
	if ($('name_to_use') !== null) { // logged user
		var params = new Hash();
		params.name = name;
		params.value = value;
		new Ajax.Request('/users/savepref', {
			method: 'post',
			parameters: params
		});
	}
}

function catfind() {
	if( document.getElementById("punindex") ) {
		var pref_save;
		if (LireCookie("punbb_dyncat")) {
			var cookie_value = LireCookie("punbb_dyncat");
			pref_save = cookie_value.split('_');
		} else {
			pref_save = [];
		}

		var nbcat = ArrayIdx();
		var f1 = function() {
			dyncat(this.h, this.t);
			var pref_save = pref.join('_');
			EcrireCookie("punbb_dyncat", pref_save, date);
                        SavePref("punbb_dyncat", pref_save);
		};

		for(var i=0; i < nbcat; i++)
		{
			pref[i] = (pref_save[i]) ? pref_save[i] : 1;
		
			var indice = i + 1;
                        var h;
			if (document.getElementById("announce")) 
				{ h = indice; }
			else
				{ h = i; }

			var h2 = document.getElementsByTagName("h2")[h];
			var table = document.getElementsByTagName("table")[i];
			var dh = h2.getElementsByTagName("span")[0].firstChild.data;
		
			if(pref[i] == 1) {
				h2.innerHTML = '<span class="picto picto_close"></span> <span>' + dh + '</span>';
				table.style.display='';
			} else {
				h2.innerHTML = '<span class="picto picto_open"></span> <span>' + dh + '</span>';
				table.style.display='none';
			}
		
			h2.h = h;
			h2.t = i;

			h2.setAttribute("onclick", f1);
			h2.onclick = f1;
		}
	}
}
	
if (window.attachEvent) {
	window.attachEvent("onload",catfind);
} else if (window.addEventListener) {
	window.addEventListener("load",catfind, false);
}
