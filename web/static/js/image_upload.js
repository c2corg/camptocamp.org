ImageUpload = {

  frame : function(c) {
    var n = 'f' + Math.floor(Math.random() * 99999);
    var d = document.createElement('div');
    d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="ImageUpload.loaded(\''+n+'\')"></iframe>';
    document.body.appendChild(d);

    var i = $(n);
    if (c && typeof(c.onComplete) == 'function') {
      i.onComplete = c.onComplete;
    }

    return n;
  },

  form : function(f, name) {
    f.setAttribute('target', name);
  },

  submit : function(f, c) {
    ImageUpload.form(f, ImageUpload.frame(c));
    if (c && typeof(c.onStart) == 'function') {
      return c.onStart();
    } else {
      return true;
    }
  },

  loaded : function(id) {
    var i = $(id);
    if (i.contentDocument) {
      var d = i.contentDocument;
    } else if (i.contentWindow) {
      var d = i.contentWindow.document;
    } else {
      var d = window.frames[id].document;
    }

    if (d.location.href == "about:blank") {
      return;
    }

    if (typeof(i.onComplete) == 'function') {
      i.onComplete(d.body.innerHTML);
    }
  },

  startCallback : function() {
    $('files_uploading').insert($F('image_file'));
    return true;
  },

  completeCallback : function(response) {
    alert('upload finished');
  }
}
