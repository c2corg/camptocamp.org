// bbcode image wizard for forums
(function(C2C, $) {
  // If the browser is modern enough, we replace the onclick event with our own function
  if (!!(window.File && window.FileList && window.FileReader)) {

    var imgur_data, i18n,
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
          title: $(this).attr('data-wizard-title')
        });
        $(document).on('keyup.fiw', function(e) {
          if (e.which == 27) {
            close_images_wizard();
          }
        }).on('click.fiw', '[data-dismiss="modal"]', function() {
          close_images_wizard();
        });
      });

    C2C.init_forums_images_wizard = function(_i18n) {
      var file_input = $('#images_wizard_file'),
          file_button = $('#images_wizard_select_file'),
          url_input = $('#images_wizard_url_input');

      i18n = _i18n;

      // click to select a local file
      file_input.change(function() {
        upload_local_file(this.files);
      });
      file_button.click(function() {
        file_input.click();
      });

      // easy focus for input field
      $('#images_wizard_url').click(function() {
        url_input.focus();
      });

      // detect if user pasted an url
      url_input.on('paste.fiw', function(e) {
        var that = this;
        setTimeout(function() {
          handle_url($(that).val(), true);
        }, 100);
      })
      // else wait for user to press enter
      .on('keyup.fiw', function(e) {
        if (e.which == 13) {
          handle_url($(this).val());
        }
      });

      $(document).on('dragenter.fiw', function(e) {
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
        if (e.originalEvent.dataTransfer.files.length) {
          upload_local_file(e.originalEvent.dataTransfer.files);
        } else if (e.originalEvent.dataTransfer.getData('URL')) {
          handle_url(e.originalEvent.dataTransfer.getData('URL'));
        } else {
          error();
        }
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

    if (!file || !file.type.match(/image\/(jpe?g|png|gif)|application\/pdf/)) {
      error();
      return;
    }

    // note: I tried to simply use FormData rather than retrieving the base64, but then there is
    // a problem with CORS, not sure exactly why, since it accepts our preflighted requests with
    // current method
    // anyway, browser support for FileReader or FormData is the same
    var reader = new FileReader();

    reader.onload = function(imgdata) {
      var progress = $('<div class="progress"/>');
      $('#images_wizard')
        .append($('<div><br/><br/>'+i18n.wait+'<br/><br/></div>'))
        .append($('<div class="progress_bar"/>')
          .append(progress))
        .find('ul').hide();

      var base64 = imgdata.target.result.substr(imgdata.target.result.indexOf('base64,')+7);
      imgurUpload(base64).done(function(result) {
        // check if modal is still there and has not been closed
        if ($('#modalbox').hasClass('in')) {
          imgur_data = result.data;
          insert_imgur_code();
        } else {
          clean_imgur(result.data.deletehash);
        }
      }).fail(function() {
        // check if modal is still there and has not been closed
        if ($('#modalbox').hasClass('in')) {
          error();
          C2C.insert_text('[img]', '[/img]');
          close_images_wizard();
        }
      }).progress(function(p) {
        progress.width(p + '%');
      });
    };
    reader.readAsDataURL(file);
  }

  function handle_url(url, pasted) {
    var parts, criteria, indicator = $('#indicator');

    // url of a direct link to c2c image
    if (parts = url.match(/^https?:\/\/\w+\.camptocamp\.org\/uploads\/images\/([0-9]{10}_[0-9]+)(SI|MI|BI)?.(jpg|png|gif)/)) {
      criteria = 'filename/' + parts[1] + '.' + parts[3];
    }
    // c2c image page
    else if (parts = url.match(/^https?:\/\/\w+\.camptocamp\.org\/images\/([0-9]+)/)) {
      criteria = 'id/' + parts[1];
    }
    // does it looks like an url?
    else if (url.match(/^https?:\/\/.*\.(jpe?g|png|gif)$/i)) {
      insert_url_code(url);
      return;
    }
    // obviously not a valid url, let the user continue typing
    else {
      if (!pasted) {
        error();
      }
      return;
    }

    indicator.show();
    $.get('/images/find/' + criteria).done(function(data) {
      C2C.insert_text('[img=' + data.filename + ' ' + data.id + ' inline]', '[/img]');
      close_images_wizard();
    }).fail(function() {
      error();
    }).always(function() {
      indicator.hide();
    });
  }

  function error() {
    C2C.showFailure(i18n.failure);
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
    C2C.insert_text('[url=http://imgur.com/'+imgur_data.id+'][img='+parts[1]+'m'+parts[2]+']', '[/img][/url]');

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
