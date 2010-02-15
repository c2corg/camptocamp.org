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
    if (!ImageUpload.validateFilename($F('image_file'))) {
      $('image_selection').down('.image_form_error').show();
      return false;
    }
    $('image_selection').down('.image_form_error').hide();
    upload_id = ImageUpload.frame(c);
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

  validateFilename : function(name) {
    if (name == '') return false;
    reg = /\.(png|jpeg|jpg|gif|svg)$/i;
    return reg.test(name);
  },

  validateImageForms : function(pe) {
    if ($('MB_content') == null) {
      pe.stop();
      return null;
    }

    var allow_submit = true;
    var images = $$('.image_upload_entry input');
    if (images.length > 0) {
      $$('.image_upload_entry').each(function(obj) {
        // if not displayed, removed it from dom (because BlindUp doesn't removes from dom)
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
      $('images_submit').disabled = !allow_submit;
    } else {
      $('images_submit').disabled = true;
    }
  },

  startCallback : function(upload_id, f) {
    // create entry for the image
    var loadingImg = new Element('img', { src: '/static/images/indicator.gif' });
    var fileText = new Element('span');
    fileText.update($F('image_file')+' ');
    var imageDiv = new Element('div', { id: 'u'+upload_id, className: 'image_upload_entry' });

    imageDiv.appendChild(fileText);
    imageDiv.appendChild(loadingImg);
    $('files_to_upload').insert({ top: imageDiv });

    return true;
  },

  completeCallback : function(upload_id, response) {
    $('image_number').writeAttribute('value', parseInt($F('image_number')) + 1);
    $('u'+upload_id).update(response);
    new Effect.Highlight('u'+upload_id);
  },

  onchangeCallback: function() {
    if (ImageUpload.submit($('form_file_input'), {
          'onStart' : ImageUpload.startCallback,
          'onComplete' : ImageUpload.completeCallback
        })) {
      $('form_file_input').submit();

      // empty file input and visual effect
      $('image_selection').hide();
      $('image_selection').down('label').update($('image_add_str').innerHTML);
      $('form_file_input').reset();
      function show_new_input_file() { new Effect.Appear('image_selection'); }
      show_new_input_file.delay(1.5);

    }
  }
}
