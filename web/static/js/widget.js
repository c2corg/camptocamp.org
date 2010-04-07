window.c2cwgt = {};

window.c2cwgt.callExternalScript = function (url) {
  var n = document.createElement("script");
  n.setAttribute("type", "text/javascript");
  n.setAttribute("src", url);
  document.getElementsByTagName("head")[0].appendChild(n);
}

window.c2cwgt.callExternalCss = function (url) {
  var n = document.createElement("link");
  n.setAttribute("type", "text/css");
  n.setAttribute("href", url);
  n.setAttribute("rel", "stylesheet");
  document.getElementsByTagName("head")[0].appendChild(n);
}

window.c2cwgt.insertContent = function (content) {
  document.getElementById('c2cwgt').removeChild(document.getElementById('c2cwgt_loading'));
  var inserted = document.createElement("ul");
  for (var i = 1; i < content.length; i++) {
    var li = document.createElement("li");
    var a = document.createElement("a");
    var item = content[i];
    a.href=item[1];
    a.innerHTML = item[0];
    li.appendChild(a);
    inserted.appendChild(li);
  }
  document.getElementById('c2cwgt').insertBefore(inserted, document.getElementById('c2cwgt_link'));
}

var title = c2cwgt_title || 'camptocamp.org';
document.write('<div id="c2cwgt"><h1>' + title + '</h1>');
document.write('<div id="c2cwgt_loading"></div>');
document.write('<a id="c2cwgt_link" href="http://www.camptocamp.org" title="http://www.camptocamp.org"><img src="http://www.camptocamp.org/static/images/logo_mini.png"></a>');
document.write('</div>');

window.c2cwgt.callExternalCss("http://localhost/static/css/c2cwgt.css");

var url = "http://www.camptocamp.org/" + c2cwgt_module + "/widget/" + c2cwgt_lang + "/" + c2cwgt_params;
if (!c2cwgt_params.match("npp")) {
  url = url + "/npp/15";
}
window.c2cwgt.callExternalScript(url);
