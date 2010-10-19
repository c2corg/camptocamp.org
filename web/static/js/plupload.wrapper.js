PlUploadWrapper = {

  image_number : 0,

  init : function(upload_url, backup_url, backup_js) {
    PlUploadWrapper.backup_url = backup_url;
    PlUploadWrapper.backup_js = backup_js;
    var uploader = new plupload.Uploader({
      runtimes : 'silverlight,html5,flash', // TODO
      browse_button : 'pickfiles',
      container : 'container',
      file_data_name : 'image_file',
      multipart : true,
      url : upload_url,
      flash_swf_url : '/static/js/plupload/plupload.flash.swf', // TODO would be great to use _static_url, but doesn't seem to work
      silverlight_xap_url : '/static/js/plupload/plupload.silverlight.xap',
      multipart_params: { image_number : PlUploadWrapper.image_number },
      filters : [
        { title : "Image files", extensions : "JPEG,jpeg,JPG,jpg,GIF,gif,PNG,png,SVG,svg" } // TODO i18n
      ],
      required_features : 'pngresize,jpgresize,progress,multipart', // a runtime that doesn't have one of this feature will fail
      resize : { width : 4096, height : 1024, quality : 90 } // TODO add some server side work to enhance images quality?
      // TODO check that the resized image is unlikely to be >2mb, even with PNG files
    });

    uploader.bind('Init', function(up, params) {
      if ($('filelist'))
        $('filelist').update("<div>Current runtime: " + params.runtime + "</div>");
    });

    uploader.bind('Error', function(up, err) {
      // no available runtime with all desired features,
      // load needed js and redirect to backup upload system
      if (err.code == plupload.INIT_ERROR) {
        var script = new Element('script', { type : 'text/javascript', src : backup_js });
        document.getElementsByTagName('head')[0].appendChild(script);
        Modalbox.show(PlUploadWrapper.backup_url);
        return;
      } else if (err.code == plupload.FILE_SIZE_ERROR) {
        // TODO
      } else if (err.code == plupload.FILE_EXTENSION_ERROR) {
        // TODO
        //$(err.file.id).hide();
      } else {
        // other errors TODO TODO
        $('filelist').insert("<div>Error: " + err.code +
          ", Message: " + err.message +
          (err.file ? ", File: " + err.file.name : "") +
          "</div>"
        );
      }
      up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.init();

    uploader.bind('UploadFile', function(up, file) {
      // increment image_number
      PlUploadWrapper.image_number++;
      up.settings.multipart_params = { image_number: PlUploadWrapper.image_number };
      // gif is not resizable, prevent uploading too big files
      if (/\.(gif|svg)$/i.test(file.name)) {
        up.settings.max_file_size = '2mb';
        // TODO could be goo if we could check image dimensions. Anyway, this will not happen often...
      }
      // TODO better behaviour for SVGs? (eg use chunking and allow files up to xx mb)
    });

    uploader.bind('FilesAdded', function(up, files) {
      files.each(function(file, i) {
        var loadingImg = new Element('img', { src: _static_url + '/static/images/indicator.gif' });
        var loadingText = new Element('span').update(
              file.name + ' (' + plupload.formatSize(file.size) +
              ') <b>0%</b>' + '</div>');
        var loadingDiv = new Element('div', { id: file.id });
        loadingDiv.appendChild(loadingImg);
        loadingDiv.appendChild(loadingText);
        $('files_to_upload').insert({ top: loadingDiv });
      });
      up.refresh(); // Reposition Flash/Silverlight
      uploader.start(); // automatically begin upload
    });

    uploader.bind('UploadProgress', function(up, file) {
      if ($(file.id).down('b')) {
        $(file.id).down('b').replace('<b>' + file.percent + "%</b>");
      }
    });

    uploader.bind('FileUploaded', function(up, file, response) {
      var elt = $(file.id);
      elt.update(response.response);
      new Effect.Highlight(elt);
    });
  },

  // same function as in images_upload.js
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
  }
}
