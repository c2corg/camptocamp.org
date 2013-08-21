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
    if (!ImageUpload.validateFilename()) {
      $('image_selection').down('.image_form_error').show();
      return false;
    }
    $('image_selection').down('.image_form_error').hide();
    var upload_id = ImageUpload.frame(c);
    ImageUpload.form(f, upload_id);
    if (c && typeof(c.onStart) == 'function') {
      return c.onStart(upload_id, f);
    } else {
      return true;
    }
  },

  // called when the iframe has finished loading
  loaded : function(id) {
    var i = $(id);
    var d;
    if (i.contentDocument) {
      d = i.contentDocument;
    } else if (i.contentWindow) {
      d = i.contentWindow.document;
    } else {
      d = window.frames[id].document;
    }

    if (d.location.href == 'about:blank') {
      return;
    }

    if (typeof(i.onComplete) == 'function') {
      i.onComplete(id, d.body.innerHTML);
    }
  },

  validateFilename : function() {
    var reg = /\.(png|jpeg|jpg|gif|svg)$/i;
    // test if file api is implemented
    if ($('image_file').files) {
      var files = $('image_file').files;
      if (files.length > 4) return false;
      for (var i = 0; i < files.length; i++) {
        if (!reg.test(files[i].name)) return false;
      }
      return true;
    } else {
      name = $F('image_file');
      if (name == '') { return false; }
      return reg.test(name);
    }
  },

  validateImageForms : function(pe) {
    if ($('MB_content') === null) {
      pe.stop();
      return null;
    }

    var allow_submit = true;
    var images = $$('.image_upload_entry input');
    if (images.length > 0) {
      $$('.image_upload_entry').each(function(obj) {
        // if not displayed, remove it from dom (because BlindUp doesn't remove from dom)
        if (obj.style.display == 'none') {
          obj.remove();
          return;
        }
        if (obj.down('input')) {
          if (obj.down('input').value.length < 4) {
            obj.down('.image_form_error').show();
            allow_submit = false;
          } else {
            obj.down('.image_form_error').hide();
          }
        }
      });
      if (allow_submit) {
        $$('.images_submit').invoke('enable');
      } else {
        $$('.images_submit').invoke('disable');
      }
    } else {
      $$('.images_submit').invoke('disable');
    }
  },

  startCallback : function(upload_id, f) {
    // create entry for the image
    var loadingImg = new Element('img', { src: _static_url + '/static/images/indicator.gif' });
    var fileText = new Element('span');
    // file names
    if ($('image_file').files) {
      var fa = [];
      for (var i = 0; i < $('image_file').files.length; i++) {
        fa[i] = $('image_file').files[i].name;
      }
      var filenames = fa.join(', ');
    } else {
      var filenames = $F('image_file')
    }
    fileText.update(filenames+' ');
    var imageDiv = new Element('div', { id: 'u'+upload_id });

    imageDiv.appendChild(fileText);
    imageDiv.appendChild(loadingImg);
    $('files_to_upload').insert({ top: imageDiv });

    return true;
  },

  completeCallback : function(upload_id, response) {
    $('u'+upload_id).update(response);
    new Effect.Highlight('u'+upload_id);
  },

  showNewInputFile : function(image_number) {
    $('image_number').writeAttribute('value', image_number);
    new Effect.Appear('image_selection');
  },

  onchangeCallback: function() {
    if (ImageUpload.submit($('form_file_input'), {
          'onStart' : ImageUpload.startCallback,
          'onComplete' : ImageUpload.completeCallback
        })) {
      $('form_file_input').submit();
      var image_number = parseInt($F('image_number'), 10) + 1;

      // empty file input and visual effect
      $('image_selection').hide();
      $('image_selection').down('label').update($('image_add_str').innerHTML);
      $('form_file_input').reset();

      ImageUpload.showNewInputFile.delay(1.5, image_number);
    }
  }
};
alert('plop');
