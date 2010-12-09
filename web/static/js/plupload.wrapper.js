PlUploadWrapper = {

  image_number : 0,

  init : function(upload_url, backup_url, backup_js, i18n) {
    PlUploadWrapper.backup_url = backup_url;
    PlUploadWrapper.backup_js = backup_js;
    PlUploadWrapper.i18n = i18n;
    var uploader = new plupload.Uploader({
      runtimes : 'html5', //'silverlight,html5,flash', FIXME reenable flash and silverlight runtimes once they support exif
      browse_button : 'pickfiles',
      container : 'container',
      file_data_name : 'image_file',
      multipart : true, // TODO maybe disable because of chrome? Then use headers instead of multipart_params
      url : upload_url,
      flash_swf_url : _static_url + '/static/js/plupload/plupload.flash.swf',
      silverlight_xap_url : _static_url + '/static/js/plupload/plupload.silverlight.xap',
      filters : [
        { title : PlUploadWrapper.i18n.extensions, extensions : "JPEG,jpeg,JPG,jpg,GIF,gif,PNG,png,SVG,svg" }
      ],
      required_features : 'pngresize,jpgresize,progress,multipart' // a runtime that doesn't have one of these features will fail
      // TODO add some server side work to enhance images quality?
    });

    uploader.bind('Init', function(up, params) {
      if ($('filelist'))
        $('filelist').update("<div>Current runtime: " + params.runtime + "</div>");
      Modalbox.resizeToContent();
      $('pickfiles').disabled = false;
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

        // file is with wrong extension, or too big (svg and gifs)
        case plupload.FILE_SIZE_ERROR:
        case plupload.FILE_EXTENSION_ERROR:
          PlUploadWrapper.displayError(err.file, PlUploadWrapper.i18n.badselect);
          break;

        // other errors
        default:
          PlUploadWrapper.displayError(err.file, PlUploadWrapper.i18n.unknownerror + ' (' + err.message + ')');
          break;
      }
      up.refresh(); // Reposition Flash/Silverlight
    });

    uploader.init();

    uploader.bind('BeforeUpload', function(up, file) {
      // increment image_number
      PlUploadWrapper.image_number++;
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
        // TODO better behaviour for SVGs? (eg use chunking and allow files up to xx mb)
      }
    });

    uploader.bind('FilesAdded', function(up, files) {
      files.each(function(file, i) {
        // do not display files that have been rejected
        if (file.status != plupload.FAILED) {
          var loadingImg = new Element('img', { src: _static_url + '/static/images/indicator.gif' }); // TODO find some better graphics..
          var loadingText = new Element('span').update(
                file.name + ' (' + plupload.formatSize(file.size) +
                ') <b>0%</b>' + '</div>');
          var loadingDiv = new Element('div', { id: file.id });
          loadingDiv.appendChild(loadingImg);
          loadingDiv.appendChild(loadingText);
          $('files_to_upload').insert({ top: loadingDiv });
        }
      });
      up.refresh(); // Reposition Flash/Silverlight
      uploader.start(); // automatically begin upload
      Modalbox.resizeToContent();
    });

    //uploader.bind('StateChanged', function(up) { alert(up.state); });

    // display upload progress
    uploader.bind('UploadProgress', function(up, file) {
      if ($(file.id).down('b')) {
        $(file.id).down('b').replace('<b>' + file.percent + '%</b>');
      }
    });

    // show server response
    uploader.bind('FileUploaded', function(up, file, response) {
      if ($(file.id).down('b')) {
        $(file.id).down('b').replace('<b>100%</b>');
      }
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
    var link = new Element('a', { onclick : 'new Effect.BlindUp($(this).up()); Modalbox.resizeToContent(); return false;',
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

    $('files_to_upload').insert({ top: div });
    Modalbox.resizeToContent();
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
      if ($('images_submit')) {
        $('images_submit').disabled = !allow_submit;
      }
    } else {
      if ($('images_submit')) {
        $('images_submit').disabled = true;
      }
    }
  }
}
