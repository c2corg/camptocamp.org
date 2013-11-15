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

  // uplaod a local file to imgur
  C2C.upload_local_file = function(files) {
    var file = files[0];

    if (!file.type.match(/image.*/)) {
      return;
    }

    var reader = new FileReader();

    reader.onload = function(imgdata) {
      var base64 = imgdata.target.result.substr(imgdata.target.result.indexOf('base64,')+7);
      imgurUpload(base64).done(function(result) {
        imgur_deletehash = result.data.deletehash;
        $('#images_wizard_url').val(result.data.link).prop('disabled', true);
        $('#images_wizard_select').prop('disabled', true);
      }).fail(function(jqXHR) {
        alert('Sorry but an error occure, when uploading image on imgur');
        C2C.insertText('[img]', '[/img]');
        C2C.close_images_wizard();
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

  function imgurUpload(imgdata) {
    var indicator = $('#indicator');
    indicator.show();
    return $.ajax({
      url: 'https://api.imgur.com/3/image',
      method: 'POST',
      headers: headers,
      data: {
        image: imgdata,
        type: 'base64'
      }
    }).always(function() {
      indicator.hide();
    });
  }

})(window.C2C = window.C2C || {}, jQuery);
