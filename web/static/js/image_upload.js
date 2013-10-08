// upload images using iframes. Obsoleted by plupload, but will still be used by ie8&9

(function(C2C, $) {

  C2C.ImageUpload = {

    pe: null,

    init: function() {
       pe = setInterval(C2C.ImageUpload.validateImageForms, 500);
    },

    // construct the iframe object that will store the form result
    frame: function(c) {
      var n = 'f' + Math.floor(Math.random() * 99999);
      var i = $('<iframe/>', {
        style: 'display:none',
        src: 'about:blank',
        id: n,
        name: n,
        onload: "C2C.ImageUpload.loaded('"+n+"')"
      });
      $('body').append($('<div/>').append(i));

      if (c && typeof(c.onComplete) == 'function') {
        i[0].onComplete = c.onComplete;
      }

      return n;
    },

    // set target attribute to form, so that its result is loaded in the iframe,
    // keeping current page open
    form: function(f, name) {
      f.attr('target', name);
    },

    // submit the form
    submit: function(f, c) {
      if (!C2C.ImageUpload.validateFilename()) {
        $('image_selection .image_form_error').show();
        return false;
      }

      $('image_selection .image_form_error').hide();
      var upload_id = C2C.ImageUpload.frame(c);
      C2C.ImageUpload.form(f, upload_id);
      if (c && typeof(c.onStart) == 'function') {
        return c.onStart(upload_id, f);
      } else {
        return true;
      }
    },

    // called when the iframe has finished loading
    loaded: function(id) {
      var i = document.getElementById(id), d;

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

    validateFilename: function() {
      var reg = /\.(png|jpeg|jpg|gif|svg)$/i;
      // test if file api is implemented
      var files = document.getElementById('image_file').files;
      if (files) {
        if (files.length > 4) return false;
        for (var i = 0; i < files.length; i++) {
          if (!reg.test(files[i].name)) return false;
        }
        return true;
      } else {
        name = document.getElementById('image_file').value;
        if (name === '') { return false; }
        return reg.test(name);
      }
    },

    validateImageForms: function(pe) {
      if (!$('#modalbox').hasClass('in')) { // means modalbox is closed
        clearInterval(pe);
        return null;
      }

      var allow_submit = true;

      $('.image_upload_entry').each(function() {
        if (this.style.display == 'none') {
          $(this).remove();
          return;
        }
        if ($(this).find('input').length) {
          allow_submit = ($(this).find('input').val().length >= 4);
          $(this).find('.image_form_error').toggle(!allow_submit);
        }
      });

      if (allow_submit && $('.image_upload_entry input').length) {
        $('.images_submit').removeAttr('disabled');
      } else {
        $('.images_submit').attr('disabled', 'disabled');
      }
    },

    startCallback: function(upload_id, f) {
      // create entry for the image
      // file names
      var files = document.getElementById('image_file').files, filenames;
      if (files) {
        var fa = [];
        for (var i = 0, l = files.length; i < l; i++) {
          fa[i] = files[i].name;
        }
        filenames = fa.join(', ');
      } else {
        filenames = document.getElementById('image_file').value;
      }
                
      $('#files_to_upload').prepend($('<div id="u'+upload_id+'"/>')
        .append('<span>'+filenames+' </span>',
                '<img src="' + _static_url + '/static/images/indicator.gif" />'));

      return true;
    },

    completeCallback: function(upload_id, response) {
      $('#u'+upload_id).html(response)
        .find('.tmp-image-close').click(function() { $(this).parent().remove(); });
    },

    showNewInputFile: function(image_number) {
      $('#image_number').attr('value', image_number);
      $('#image_selection').fadeIn();
    },

    onchangeCallback: function() {
      if (C2C.ImageUpload.submit($('#form_file_input'), {
            'onStart': C2C.ImageUpload.startCallback,
            'onComplete': C2C.ImageUpload.completeCallback
          })) {
        $('#form_file_input').submit();
        var image_number = parseInt($('#image_number').val(), 10) + 1;

        // empty file input and visual effect
        $('#image_selection').hide();
        $('#image_selection label').html($('#image_add_str').html());
        document.getElementById('form_file_input').reset();

        window.setTimeout(function() { C2C.ImageUpload.showNewInputFile(image_number); }, 1500);
      }
    }
  };

})(window.C2C = window.C2C || {}, jQuery);
