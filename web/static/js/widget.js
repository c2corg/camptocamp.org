window.c2cwgt = {};

window.c2cwgt.callExternalScript = function (url) {
  var n = document.createElement("script");
  n.setAttribute("type", "text/javascript");
  n.setAttribute("src", url);
  document.getElementsByTagName("head")[0].appendChild(n);
}

window.c2cwgt.callExternalCss = function (url) {
  for (link in document.getElementsByTagName("link")) {
    if (link.href == url) {
      return;
    }
  }
  var n = document.createElement("link");
  n.setAttribute("type", "text/css");
  n.setAttribute("href", url);
  n.setAttribute("rel", "stylesheet");
  document.getElementsByTagName("head")[0].appendChild(n);
}

window.c2cwgt.insertContent = function (id, content) {
  var div = document.getElementById(id);
  div.removeChild(div.childNodes[1]);
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
  div.insertBefore(inserted, div.childNodes[1]);
}

function showC2CWidget (content) {
  window.c2cwgt.callExternalCss("http://www.camptocamp.org/static/css/c2cwgt.css");

  var title = content.title || 'camptocamp.org';
  var div = document.getElementById(content.div);
  var inserted = '<h1>' + title + '</h1>'
    + '<div class="c2cwgt_loading"></div>'
    + '<a class="c2cwgt_link" href="http://www.camptocamp.org" title="http://www.camptocamp.org">'
    + '<img src="http://www.camptocamp.org/static/images/logo_mini.png"></a>';
    div.innerHTML = inserted;
    div.className = "c2cwgt";

  var url = "http://www.camptocamp.org/" + content.module + "/widget";
  if (content.params) {
    url = url + "/" + content.params;
  }
  if (!content.params.match("npp")) {
    url = url + "/npp/15";
  }
  url = url + "?div=" + content.div;
  window.c2cwgt.callExternalScript(url);
}