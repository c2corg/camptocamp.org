window.c2cwgt = window.c2cwgt || {};

window.c2cwgt.callExternalScript = function (url) {
  var n = document.createElement("script");
  n.setAttribute("type", "text/javascript");
  n.setAttribute("src", url);
  document.getElementsByTagName("head")[0].appendChild(n);
};

window.c2cwgt.callExternalCss = function (url) {
  for (var link in document.getElementsByTagName("link")) {
    if (link.href == url) {
      return;
    }
  }
  var n = document.createElement("link");
  n.setAttribute("type", "text/css");
  n.setAttribute("href", url);
  n.setAttribute("rel", "stylesheet");
  document.getElementsByTagName("head")[0].appendChild(n);
};

window.c2cwgt.insertContent = function (id, content) {
  var div = document.getElementById(id);
  div.removeChild(div.childNodes[1]);
  var inserted = document.createElement("ul");
  for (var i = 0; i < content.length; i++) {
    var li = document.createElement("li");
    var a = document.createElement("a");
    var item = content[i];
    a.href=item[1];
    a.innerHTML = item[0];
    li.appendChild(a);
    inserted.appendChild(li);
  }
  div.insertBefore(inserted, div.childNodes[1]);
};

// rather put it in c2cwgt object
// but kept as is for compatibility
function showC2CWidget (content) {
  window.c2cwgt.callExternalCss("https://s.camptocamp.org/static/css/c2cwgt.css");

  var title = content.title || 'camptocamp.org';
  var div = document.getElementById(content.div);
  var inserted = '<h1>' + title + '</h1>'
    + '<div class="c2cwgt_loading"></div>'
    + '<a class="c2cwgt_link" href="https://www.camptocamp.org" title="https://www.camptocamp.org">'
    + '<img src="https://s.camptocamp.org/static/images/logo_mini.png"></a>';
    div.innerHTML = inserted;
    div.className = "c2cwgt";

  var url = "https://www.camptocamp.org/" + content.module + "/widget";
  if (content.params) {
    url = url + "/" + content.params;
  }
  if (!content.params.match("npp")) {
    url = url + "/npp/15";
  }
  url = url + "?div=" + content.div;
  window.c2cwgt.callExternalScript(url);
}

// this script should be called asynchronously, and c2cwgt.params should be declared
// if params variable doesn't exist, user is probably using a synchronous call and will
// execute callExternalScript() by himself
if (window.c2cwgt.params) {
  var params = [].concat(window.c2cwgt.params);
  for (var i=0; i<params.length; i++) {
    showC2CWidget(params[i]);
  }
}
