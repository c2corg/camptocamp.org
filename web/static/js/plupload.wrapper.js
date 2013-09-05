PlUploadWrapper = {

  /**
   * TODO / notes:
   * - html5 runtime is currently only available for firefox 3.5+ and chrome 9+, since other browser don't support multipart and image resizing
   * - silverlight runtime finally removed, because it doesn't support exif yet, and html5 || flash probably covers more than 99% of users
   * - add some server side work to enhance image quality?
   * - better behaviour for SVGs (eg enable chunking for file >2mB)
   * - use static url for swf file (but we then need crossdomain.xml file)
   */

  image_number : 0,

  init : function(upload_url, backup_url, backup_js, i18n) {
    PlUploadWrapper.backup_url = backup_url;
    PlUploadWrapper.backup_js = backup_js;
    PlUploadWrapper.i18n = i18n;
    var uploader = new plupload.Uploader({
      runtimes: 'html5,flash', // rq: flash is not working well with FF (getFlashObj() null ?) but anyway, html5 is fine with firefox
      browse_button: 'pickfiles',
      container: 'container', // when using the body as container, flash shim is badly placed when scrolling, so we attach it to the modalbox
      drop_element: 'plupload_tips',
      file_data_name: 'image_file',
      multipart: true,
      url: upload_url,
      flash_swf_url: '/static/js/plupload/plupload.flash.swf',
      filters: [
        { title: PlUploadWrapper.i18n.extensions, extensions: "jpeg,jpg,gif,png,svg" }
      ],
      required_features: 'pngresize,jpgresize,progress,multipart' // a runtime that doesn't have one of these features will fail
    });

    uploader.bind('Init', function(up, params) {
      Modalbox.resizeToContent();
      $('pickfiles').disabled = false;

      // drag&drop look&feel
      if (up.features.dragdrop) {
        $$('.plupload-drag-drop')[0].show();
        var delt = $('plupload_ondrag');
        var nelt = $('plupload_normal');
        delt.style.height = (nelt.getHeight() - 12) + 'px';
        delt.style.width = (nelt.getWidth() - 12) + 'px';
        plupload.addEvent(document.documentElement, 'dragenter',
                          function() { delt.style.zIndex = 1; });
        /* Idea here would be to use dragleave event, but someone thought that it would
           be funnier to fire dragleave when hovering child elements...
           Instead, we hide delt when mouse goes out of document */
        plupload.addEvent(document.documentElement, 'mouseout',
                          function() { delt.style.zIndex = -1; });
      }
    });

    uploader.bind('Error', function(up, err) {
      switch(err.code) {
        // no available runtime with all desired features,
        // load needed js and redirect to backup upload system
        case plupload.INIT_ERROR:
          var script = new Element('script', { type : 'text/javascript', src : backup_js });
          document.getElementsByTagName('head')[0].appendChild(script);
          Modalbox.show(PlUploadWrapper.backup_url);
          return;

        // file is with wrong extension, or too big (svg and gif files cannot be resized)
        case plupload.FILE_SIZE_ERROR:
        case plupload.FILE_EXTENSION_ERROR:
          PlUploadWrapper.displayError(err.file, PlUploadWrapper.i18n.badselect);
          break;

        // other errors
        default:
          PlUploadWrapper.displayError(err.file, PlUploadWrapper.i18n.unknownerror + ' (' + err.message + ')');
          break;
      }
      up.refresh(); // reposition Flash/Silverlight
    });

    uploader.init();

    PlUploadWrapper.uploader = uploader;

    uploader.bind('BeforeUpload', function(up, file) {
      // increment image_number
      PlUploadWrapper.image_number++;

      if ($(file.id).down('b')) {
        $(file.id).down('b').replace('<b>' + PlUploadWrapper.i18n.sending + '</b>');
        $(file.id).down('a').remove();
      }


      up.settings.multipart_params = { plupload : true, image_number: PlUploadWrapper.image_number };
      // png and jpg images <2M will get resized only if they exceed c2c limits (8192x2048)
      // images >2M will be resized to max 4096x1024
      if (/\.(png|jpg|jpeg)$/i.test(file.name)) {
          if (file.size >= 2097152) {
              up.settings.resize = { width : 4096, height : 1024, quality : 90 }
          } else {
              up.settings.resize = { width : 8192, height : 2048, quality : 90 }
          }
      }
      // gif and svg are not resizable, prevent uploading too big files
      else if (/\.(gif|svg)$/i.test(file.name)) {
        up.settings.max_file_size = '2mb';
      }
    });

    uploader.bind('FilesAdded', function(up, files) {
      files.each(function(file, i) {
        // do not display files that have been rejected
        if (file.status != plupload.FAILED) {
          var progressBarDiv = new Element('div', { 'class': 'plupload_progress_bar' });
          var progressDiv = new Element('div', { 'class': 'plupload_progress' });
          var loadingText = new Element('span', { 'class': 'plupload_text' })
                  .update(file.name + ' <b>' + PlUploadWrapper.i18n.waiting + '</b> ');
          var cancelLink = new Element('a', { href: '#', onclick: 'PlUploadWrapper.cancelUpload(\'' + file.id + '\')' })
                  .update(PlUploadWrapper.i18n.cancel);
          var loadingDiv = new Element('div', { id: file.id });
          progressBarDiv.appendChild(progressDiv);
          loadingDiv.appendChild(progressBarDiv);
          loadingDiv.appendChild(loadingText);
          loadingDiv.appendChild(cancelLink);
          $('files_to_upload').insert({ top: loadingDiv });
        }
      });
      Modalbox.resizeToContent();
      up.refresh();  // Reposition Flash/Silverlight
      window.setTimeout(function() { up.start(); }, 500);
    });

    // display upload progress
    uploader.bind('UploadProgress', function(up, file) {
      if ($(file.id).down('b') && file.percent >= 95) {
        $(file.id).down('b').replace('<b>' + PlUploadWrapper.i18n.serverop + '</b>');
      }

      if ($(file.id).down('.plupload_progress')) {
        $(file.id).down('.plupload_progress').style.width = file.percent + 'px';
      }
    });

    // show server response
    uploader.bind('FileUploaded', function(up, file, response) {
      $$('.images_submit').invoke('show');
      var elt = $(file.id);
      elt.update(response.response);
      new Effect.Highlight(elt);
      Modalbox.resizeToContent();
    });
  },

  // function to display a self-formed error response
  displayError : function(file, errormsg) {
    var div = new Element('div', { 'class' : 'image_upload_entry' });
    var picto = new Element('span', { 'class' : 'picto action_cancel' });
    var link = new Element('a', { onclick : '$(this).up().hide(); Modalbox.resizeToContent(); return false;',
                                  href : '#',
                                  style : 'float: right;'});
    link.appendChild(picto);
    div.appendChild(link);
    div.appendChild(document.createTextNode(file.name));
    var errorDiv = new Element('div', { 'class' : 'global_form_error' });
    var ul = new Element('ul');
    var li = new Element('li').update(errormsg);
    ul.appendChild(li);
    errorDiv.appendChild(ul);
    div.appendChild(errorDiv);

    var elt = $(file.id);
    elt.update(div);
    new Effect.Highlight(elt);
    Modalbox.resizeToContent();
  },

  cancelUpload: function (file) {
    PlUploadWrapper.uploader.removeFile(PlUploadWrapper.uploader.getFile(file));
    $(file).remove();
  },

  // same function as in images_upload.js
  // used to validate with javascript that image information is correct
  // factorize? (is it worth it?)
  validateImageForms : function(pe) {
    if ($('MB_content') === null) {
      pe.stop();
      return null;
    }
    var allow_submit = true;
    var images = $$('.image_upload_entry input');
    if (images.length > 0) {
      $$('.image_upload_entry').each(function(obj) {
        // if not displayed, remove it from dom (because BlindUp doesn't remove from DOM)
        if (obj.style.display == 'none') {
          obj.remove();
          return;
        }
        if (obj.down('input')) {
          if (obj.down('input').value.replace(/^\s+|\s+$/g,"").length < 4) {
            if (!obj.down('.image_form_error').visible()) {
              obj.down('.image_form_error').show();
              Modalbox.resizeToContent();
            }
            allow_submit = false;
          } else {
            if (obj.down('.image_form_error').visible()) {
              obj.down('.image_form_error').hide();
              Modalbox.resizeToContent();
            }
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
  }
}
