// bbcode image wizard for forums
(function(C2C, $) {
  // If the browser is modern enough, we replace the onclick event with our own function
  if (!!(window.File && window.FileList && window.FileReader)) {

    var imgur_data,
        headers = {
          Authorization: 'Client-ID 7aa5a3fe49976d5',
          Accept: 'application/json'
        },
        drop_overlay = $('<div/>', { id: 'global-drop-overlay' }).appendTo('body');

    // replace handlers on [img] button
    $('input[name=Img]')
      .unbind('click').removeAttr('onclick')
      .on('click', function () {
        $.modalbox.show({
          remote: '/images/forums/wizard',
          title: this.title
        });
        $(document).on('keyup.fiw', function(e) {
          if (e.which == 27) {
            close_images_wizard();
          }
        }).on('click.fiw', '[data-dismiss="modal"]', function(e) {
          close_images_wizard();
        });
      });

    C2C.init_forums_images_wizard = function() {
      var file_input = $('#images_wizard_file'),
          file_button = $('#images_wizard_select_file'),
          url_input = $('#images_wizard_url');

      // click to select a local file
      file_input.change(function() {
        upload_local_file(this.files);
      });
      file_button.click(function() {
        file_input.click();
      });

      // detect if user pasted an url
      url_input.on('paste.fiw', function(e) {
        console.log('pasted!');
        console.log(e.target.value);
        var that = this;
        setTimeout(function() {
          var url = $(that).val();
          if (url.match(/^https?:\/\//)) { // does it looks like an url?
            insert_url_code(url);
          }
        }, 100);
      })
      // else wait for user to press enter
      .on('keyup.fiw', function(e) {
        var url = $(this).val();
        if (e.which == 13 && url.match(/^https?:\/\//)) {
          insert_url_code(url);
        }
      });

      $(document).on('dragenter.fiw', function(e) {
        e.stopPropagation();
        e.preventDefault();
        drop_overlay.addClass('active');
      }).on('dragleave.fiw', function(e) {
        if (e.target.id == drop_overlay.get(0).id) {
          drop_overlay.removeClass('active');
        }
      }).on('dragover.fiw', function(e) {
        e.stopPropagation();
        e.preventDefault();
      }).on('drop.fiw', function(e) {
        e.stopPropagation();
        e.preventDefault();
        drop_overlay.removeClass('active');
        upload_local_file(e.originalEvent.dataTransfer.files);
      });
    };
  }

  function close_images_wizard() {
    $.modalbox.hide();
    $(document).off('keyup.fiw dragenter.fiw dragover.fiw drop.fiw dragleave.fiw click.fiw');
  }

  // upload a local file to online image storage service
  function upload_local_file(files) {
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

      $('#images_wizard').append($('<img/>', { src: imgdata.target.result,
        style: 'max-width: 100px' }));

      var indicator = $('#indicator');
      indicator.show();
      imgurUpload(base64).done(function(result) {
        // check if modal is still there and has not been closed
        if ($('#modalbox').hasClass('in')) {
          imgur_data = result.data;
          $('#images_wizard_url').val(result.data.link).prop('disabled', true);
          $('#images_wizard_select').prop('disabled', true);
          insert_imgur_code();
        } else {
          clean_imgur(result.data.deletehash);
        }
      }).fail(function() {
        // check if modal is still there and has not been closed
        if ($('#modalbox').hasClass('in')) {
          alert('Sorry but an error occure, when uploading image on imgur');
          C2C.insert_text('[img]', '[/img]');
          close_images_wizard();
        }
      }).progress(function(progress) {
        console.log('progress'+progress);
      }).always(function() {
        indicator.hide();
      });
    };
    reader.readAsDataURL(file);
  }

  // insert code if image url is given
  function insert_url_code(url) {
    C2C.insert_text('[img='+url+']', '[/img]');

    close_images_wizard();
  }

  // insert imgur code into textarea
  function insert_imgur_code() {
    // we do not display original, but the small thumbnail
    // and wrap it into a link to imgur
    var parts = imgur_data.link.match(/([^\\]*)(\.\w+)$/);
    C2C.insert_text('[url=http://imgur.com/'+imgur_data.id+'][img='+parts[1]+'t'+parts[2]+']', '[/img][/url]');

    close_images_wizard();
  }

  // if user cancels, and an image has been uploaded to imgur, delete it
  function clean_imgur(deletehash) {
    $.ajax({
      url: 'https://api.imgur.com/3/image/'+deletehash,
      method: 'DELETE',
      headers: headers
    });
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
    };

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
