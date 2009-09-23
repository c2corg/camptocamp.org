ImageUpload = {

  // construct the iframe object that will store the form result
  frame : function(c) {
    var n = 'f' + Math.floor(Math.random() * 99999);
    var i = new Element('iframe', { style: 'display:none', src: 'about:blank', id: n, name: n, onload: "ImageUpload.loaded('"+n+"')" });
    var d = new Element('div');
    d.appendChild(i);
    document.body.appendChild(d);

    if (c && typeof(c.onComplete) == 'function') {
      i.onComplete = c.onComplete;
    }

    return n;
  },

  // set target attribute to form, so that its result is loaded in the iframe,
  // keeping current page open
  form : function(f, name) {
    f.setAttribute('target', name);
  },

  // submit the form
  submit : function(f, c) {
    if (!ImageUpload.validateName($F('image_file'))) {
      // TODO signal it to the user
      return false;
    }
    upload_id = ImageUpload.frame(c);
    ImageUpload.form(f, upload_id);
    if (c && typeof(c.onStart) == 'function') {
      return c.onStart(upload_id);
    } else {
      return true;
    }
  },

  // called when the iframe has finished loading
  loaded : function(id) {
    var i = $(id);
    if (i.contentDocument) {
      var d = i.contentDocument;
    } else if (i.contentWindow) {
      var d = i.contentWindow.document;
    } else {
      var d = window.frames[id].document;
    }

    if (d.location.href == 'about:blank') {
      return;
    }

    if (typeof(i.onComplete) == 'function') {
      i.onComplete(id, d.body.innerHTML);
    }
  },

  validateName : function(name) {
    if (name == '') return false;
    reg = /\.(png|jpeg|jpg|gif)$/i;
    return reg.test(name);
  },

  startCallback : function(upload_id) {
    // create entry for the image
    var loadingImg = new Element('img', { src: '/static/images/indicator.gif' });
    var fileText = new Element('span');
    fileText.update($F('image_file')+' ');
    var imageDiv = new Element('div', { id: 'u'+upload_id, className: 'image_upload_entry' });
    
    imageDiv.appendChild(fileText);
    imageDiv.appendChild(loadingImg);
    $('files_to_upload').appendChild(imageDiv);

    //Form.reset('form_file_input');

    return true;
  },

  completeCallback : function(upload_id, response) {
    $('image_number').writeAttribute('value', parseInt($F('image_number')) + 1); // TODO
    $('u'+upload_id).update(response);
    new Effect.Highlight('u'+upload_id); // TODO
  }
}
