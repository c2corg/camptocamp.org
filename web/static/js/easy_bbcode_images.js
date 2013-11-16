// bbcode image wizard for forums
(function(C2C, $) {
  // If the browser is modern enough, we replace the onclick event with our own function
  if (!!(window.File && window.FileList && window.FileReader)) {
    $('input[name=Img]')
      .unbind('click').removeAttr('onclick')
      .on('click', function () {
        $.modalbox.show({
          remote: '/images/forums/wizard',
          title: this.title
        });
        $(document).on('keyup.wizard', function(e) {
          if (e.which == 27) {
            C2C.close_images_wizard();
          }
        });
      });

    var imgur_deletehash = null,
        headers = {
          Authorization: 'Client-ID 7aa5a3fe49976d5',
          Accept: 'application/json'
        };
  }

  C2C.close_images_wizard = function() {
    clean_imgur();
    $.modalbox.hide();
    $(document).off('keyup.wizard');
    $('#images_wizard').off('dragenter dragover drop');
  };

  // upload a local file to imgur
  C2C.upload_local_file = function(files) {
    var file = files[0];

    if (!file || !file.type.match(/image.*/)) {
      return;
    }

    // note: I tried to simply use FormData rather than retrieving the base64, but then there is
    // a problem with CORS, not sure exactly why, since it accepts our preflighted requests with
    // current method
    // anyway, browser support for FileReader or FormData is the same
    var reader = new FileReader();

    reader.onload = function(imgdata) {
      var base64 = imgdata.target.result.substr(imgdata.target.result.indexOf('base64,')+7);

      var indicator = $('#indicator');
      indicator.show();
      imgurUpload(base64).done(function(result) {
        imgur_deletehash = result.data.deletehash;
        $('#images_wizard_url').val(result.data.link).prop('disabled', true);
        $('#images_wizard_select').prop('disabled', true);
      }).fail(function(jqXHR) {
        alert('Sorry but an error occure, when uploading image on imgur');
        C2C.insert_text('[img]', '[/img]');
        C2C.close_images_wizard();
      }).progress(function(progress) {
        console.log('progress'+progress);
      }).always(function() {
        indicator.hide();
      });
    };
    reader.readAsDataURL(file);
  }

  // insert code into field
  C2C.insert_image_code = function() {
    var caption = $('#images_wizard_caption').val(),
        link = $('#images_wizard_url').val(),
        code = caption ? '[img='+link+']'+caption : '[img]'+link;

    C2C.insert_text(code, '[/img]');
    imgur_deletehash = null;
    C2C.close_images_wizard();
  };

  // if user cancels, and an image has been uploaded to imgur, delete it
  function clean_imgur() {
    if (imgur_deletehash) {
      $.ajax({
        url: 'https://api.imgur.com/3/image/'+imgur_deletehash,
        method: 'DELETE',
        headers: headers
      });
      imgur_deletehash = null;
    }
  }

  // note that we hacked it a bit so that we can use 'progress'
  // callbacks. We cannot directly use Deferred.notify on this so
  // we fake it.
  function imgurUpload(imgdata) {
    var progressClbks = [];
    var req = $.ajax({
      url: 'https://api.imgur.com/3/image',
      method: 'POST',
      headers: headers,
      xhr: function() { // 'hack' for upload progress events
        var req = $.ajaxSettings.xhr();
        if (req && req.upload) {
          req.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
              notifyProgress(parseInt(e.loaded / e.total * 100, 10));
            }
          }, false);
        }
        return req;
      },
      data: {
        image: imgdata,
        type: 'base64'
      }
    });

    req.progress = function(f) {
      progressClbks.push(f);
      return req;
    }

    function notifyProgress(progress) {
      var i = 0, length = progressClbks.length;
      for (; i < length; i++) {
        if (typeof progressClbks[i] == 'function') {
          progressClbks[i].call(req, progress);
        }
      }
    }

    return req;
  }

})(window.C2C = window.C2C || {}, jQuery);
