function dyncat(h, t, titre) {
	var h2 = document.getElementsByTagName("h2")[h];
	var table = document.getElementsByTagName("table")[t];
	
	if (table.style.display=='none') {
		table.style.display=''; 
		h2.getElementsByTagName("span")[0].innerHTML = '<img src="' + pun_static_url + '/forums/img/dyncat/min.gif" title="Réduire" /> ' + titre;
		pref[t] = 1;
	} else {
		table.style.display='none';
		h2.getElementsByTagName("span")[0].innerHTML = '<img src="' + pun_static_url + '/forums/img/dyncat/plus.gif" title="Développer" /> ' + titre;
		pref[t] = 0;
	}
}

function ArrayIdx() {
	var category = new Array();	
	for ( i = 1; document.getElementById("idx"+i) != null; i++)
	{
	if( document.getElementById("idx"+i) != null)
		{
			category[""+i] = document.getElementById("idx"+i);
		}
	}
	category.shift();
	return category.length;
}

function SavePref(name, value) {
	if ($('name_to_use') != null) { // logged user
		var params = new Hash();
		params.name = name;
		params.value = value;
		new Ajax.Request('/users/savepref', {
			method: 'post',
			parameters: params
		});
	}
}

date = new Date;
date.setFullYear(date.getFullYear()+1);

var pref = new Array();

function catfind() {
	if( document.getElementById("punindex") ) {
	
		if (LireCookie("punbb_dyncat")) {
			var cookie_value = LireCookie("punbb_dyncat");
			var pref_save = cookie_value.split('_');
		} else {
			var pref_save = new Array();
		}

		var nbcat = ArrayIdx();

		for(i=0; i < nbcat; i++)
		{
			(pref_save[i]) ? pref[i] = pref_save[i] : pref[i] = 1;
		
			var indice = i + 1;
			if (document.getElementById("announce")) 
				{ var h = indice; }
			else
				{ var h = i; }

			var h2 = document.getElementsByTagName("h2")[h];
			var table = document.getElementsByTagName("table")[i];
			var dh = h2.getElementsByTagName("span")[0].firstChild.data;
		
			if(pref[i] == 1) {
				h2.getElementsByTagName("span")[0].innerHTML = '<img src="' + pun_static_url + '/forums/img/dyncat/min.gif" title="Réduire" /> ' + dh;
				table.style.display='';
			} else {
				h2.getElementsByTagName("span")[0].innerHTML = '<img src="' + pun_static_url + '/forums/img/dyncat/plus.gif" title="Développer" /> ' + dh;
				table.style.display='none';
			}
		
			h2.h = h;
			h2.t = i;
			h2.titre = dh;
			h2.setAttribute("onclick",function() {
				dyncat(this.h, this.t, this.titre);
				var pref_save = pref.join('_');
				EcrireCookie("punbb_dyncat", pref_save, date);
                                SavePref("punbb_dyncat", pref_save);
			});
			h2.onclick = function() {
				dyncat(this.h, this.t, this.titre);
				var pref_save = pref.join('_');
				EcrireCookie("punbb_dyncat", pref_save, date);
                                SavePref("punbb_dyncat", pref_save);
			};
		}
	}
}
	
	document.write('<'+'style type="text/css" media="screen">');
	document.write('.hide { display: none; }');
	document.write('<'+'/style>');
	document.write('<div class="hide"><img src="' + pun_static_url + '/forums/img/dyncat/plus.gif" /><img src="' + 
                   pun_static_url + '/forums/img/dyncat/min.gif" /><\/div>');

if (window.attachEvent) {
	window.attachEvent("onload",catfind);
} else if (window.addEventListener) {
	window.addEventListener("load",catfind, false);
}
