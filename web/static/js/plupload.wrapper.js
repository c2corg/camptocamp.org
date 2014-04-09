(function(C2C, $) {

  /**
   * TODO / notes:
   * - html5 runtime is currently only available for recent browsers, since other browser don't support multipart and image resizing
   * - silverlight runtime reoved, since it doesn't support exif yet, and html5+flash probably covers more than 99% of users
   * - add some server side work to enhance image quality?
   * - better behaviour for SVGs (eg enable chunking for file >2mB)
   * - use static url for swf file (but we then need crossdomain.xml file)
  */

  C2C.PlUploadWrapper = function(upload_url, backup_url, i18n) {

    var dropid = 'global-drop-overlay',
        ulid = 'files_to_upload',
        image_number = 0,
        usemodalbox,
        uploader;

    function init(upload_url, backup_url, i18n) {
      setControls();

      // plupload init
      uploader = new plupload.Uploader({
        runtimes: 'html5,flash', // rq: flash is not working well with FF (getFlashObj() null ?) but anyway, html5 is fine with firefox
        browse_button: 'pickfiles',
        container: 'plupload-container', // when using the body as container, flash shim is badly placed when scrolling, so we attach it to the modalbox
        drop_element: dropid,
        file_data_name: 'image_file',
        multipart: true,
        url: upload_url,
        flash_swf_url: '/static/js/plupload/plupload.flash.swf',
        filters: [{
          title: i18n.extensions,
          extensions: "jpeg,jpg,gif,png,svg"
        }],
        required_features: 'pngresize,jpgresize,progress,multipart' // a runtime that doesn't have all of these features will fail
      });

      uploader.bind('Init', function(up) {
        $('.plupload-pickfiles').prop('disabled', false);
        $('.plupload-indication').toggle(usemodalbox);

        if (up.runtime === 'flash') {
          var button = $('.plupload-indication .plupload-pickfiles');
          button.replaceWith('<span>'+button.val()+'</span>');
        }

        // drag&drop look&feel
        if (up.features.dragdrop) {
          $('#'+dropid).remove(); // be sure it is there only once
          var drop_overlay = $('<div id="'+dropid+'"><span>'+i18n.drop+'</span></div>').appendTo('body');

          plupload.addEvent(document, 'dragenter', function() {
            if ($('#modalbox').hasClass('in') && $('#image_upload').is(':visible')) {
              drop_overlay.addClass('active');
            }
          });

          plupload.addEvent(document, 'dragleave', function(e) {
            if (e.target.id == dropid || (e.target.offsetParent && e.target.offsetParent.id == dropid)) {
              drop_overlay.removeClass('active');
            }
          });

          // be sure to hide drop_overlay even if no correct file has been dropped
          plupload.addEvent(drop_overlay[0], 'drop', function() {
            drop_overlay.removeClass('active');
          });
        } else {
          $('.plupload-drag-drop').hide();
        }
      });

      uploader.bind('Error', function(up, err) {
        switch(err.code) {
          // no available runtime with all desired features,
          // load needed js and redirect to backup upload system
          case plupload.INIT_ERROR:
            $.modalbox.show({ remote: backup_url, width: 700 });
            return;

          // file is with wrong extension, or too big (svg and gif files cannot be resized)
          case plupload.FILE_SIZE_ERROR:
          case plupload.FILE_EXTENSION_ERROR:
            displayError(err.file, i18n.badselect);
            break;

          // other errors
          default:
            displayError(err.file, i18n.unknownerror + ' (' + err.message + ' ' + err.status + ')');
            break;
        }
        up.refresh(); // reposition Flash/Silverlight
      });

      uploader.init();

      uploader.bind('BeforeUpload', function(up, file) {
        // increment image_number
        image_number++;

        var div = $('#pl_'+file.id);
        div.find('b:first').html(i18n.sending);
        div.find('a:first').remove();

        up.settings.multipart_params = {
          plupload : true,
          image_number: image_number
        };

        // png and jpg images <2M will get resized only if they exceed c2c limits (8192x2048)
        // images >2M will be resized to max 4096x1024
        if (/\.(png|jpg|jpeg)$/i.test(file.name)) {
          if (file.size >= 2097152) {
            up.settings.resize = { width : 4096, height : 1024, quality : 90 };
          } else {
            up.settings.resize = { width : 8192, height : 2048, quality : 90 };
          }
        }
        // gif and svg are not resizable, prevent uploading too big files
        else if (/\.(gif|svg)$/i.test(file.name)) {
          up.settings.max_file_size = '2mb';
        }
      });

      uploader.bind('FilesAdded', function(up, files) {
        var ul = $('#'+ulid), lis = $();

        // hide drop overlay if active
        $('#'+dropid).removeClass('active');

        $.each(files, function(i, file) {
          // do not display files that have been rejected, they will be directly handled by displayError
          // (more than that, the error event may already have fired)
          if (file.status != plupload.FAILED) {
            lis = lis.add($('<li class="plupload-entry" id="pl_'+file.id+'"/>')
              .append($('<div class="plupload-loading"/>')
                .append($('<div/>')
                  .append(
                    $('<div>'+file.name+'</div>'),
                    $('<div class="plupload-progress-bar"><div class="plupload-progress"></div></div>'),
                    $('<div><b>'+i18n.waiting+'</b></div>')
                  )
                )
                .append('<button type="button" title="'+i18n.cancel+
                        '" class="plupload-close" data-fileid="'+file.id+'">×</button>')
              )
            );
          }
        });

        if (!ul.hasClass('mixitup') && lis.length) {
          // it is better to first init mixItUp, and only then add
          // the elements, else we get one small visual glitch
          mixitup.call(ul);
        }
        ul.mixItUp('append', lis);

        up.refresh();  // Reposition Flash/Silverlight
        window.setTimeout(function() {
          up.start();
        }, 500);
      });

      // display upload progress
      uploader.bind('UploadProgress', function(up, file) {
        var li = $('#pl_'+file.id);

        li.find('.plupload-progress:first').width(file.percent + '%');

        if (file.percent >= 95) {
          li.find('b:first').html(i18n.serverop);
        }
      });

      // show server response
      uploader.bind('FileUploaded', function(up, file, response) {
        var content = $('#pl_'+file.id).html(response.response);

        // enable submit button if ok
        $('.plupload-submit').prop('disabled', !$('.plupload-entry input[name^=name]').length);

        // add timestamp info, then sort
        content.attr('data-datetime', content.find('img').attr('data-datetime') || 0);
        sort();
      });
    }

    function setControls() {

      var modalbox = $('#modalbox'),
          form = $('#files_to_upload');
      usemodalbox = modalbox.hasClass('in') && !!modalbox.has('#plupload-container').length;
      if (usemodalbox) {
        // be sure to destroy plupload when modalbox is closed
        modalbox.on('hide.modal.pl', function() {
          if (uploader) {
            uploader.destroy();
          }
          $(this).off('.pl');
          $(document).off('.pl');
          $(window).off('.pl');
        });

        // modalbox resize when window resized
        var tid;
        $(window).on('resize.pl', function() {
          clearTimeout(tid);
          tid = setTimeout(resize, 300);
        });

        form.on('click', '.plupload-cancel', $.modalbox.hide);
      } else {
        // without modalbox, cancel buton should go back in history
        form.on('click', '.plupload-cancel', function() {
          history.back();
        });
      }

      // watch changes on image title inputs
      form.on('focus', 'input[name^=name]', function() {
        $(this).on('propertychange.pl keyup.pl input.pl paste.pl', isValid);
      }).on('blur', 'input[name^=name]', function() {
        $(this).off('.pl');
      }).on('click', '.plupload-close', function() {
        var $this = $(this);
        // loading pictures have data-fileid
        var fileid = $this.attr('data-fileid');
        if (fileid) {
          cancelUpload(fileid);
        }
        removeEntry.call(this);
      });

      form.on('change', '[name^=categories]', function() {
        var $this = $(this);
        if ($this.prop('tagName') == 'SELECT') {
          var val = $this.val();
          $this.prev('.count').text(val ? val.length : 0);
        } else { // checkboxes
          var $entry = $this.closest('.plupload-entry');
          $entry.find('.count').text($entry.find('[name^=categories]:checked').length);
          $this.parent('label').toggleClass('checked', $this.prop('checked'));
        }
      }).on('change', '[name^=image_type]', function() {
        var $this = $(this), val = $this.val();
        $this.closest('.plupload-entry').find('.plupload-licence')
          .toggleClass('cc-by-nc-nd-mini', val == 2)
          .toggleClass('cc-by-sa-mini', val == 1);
        form.find('label').each(function() {
          $(this).toggleClass('checked', $(this).find('input').prop('checked'));
        });
      });

      // before submitting, check if everything is okay
      $('#images_validate_form').submit(function(event) {
        var allow_submit = titlesValid();
        if (!allow_submit) {
          // identify faulty images
          $('.plupload-entry:has(input[name^=name][data-invalid=false])').addClass('invalid');
          // display tooltip on what is going wrong
          var tooltip = $('.plupload-submit + .tooltip');
          tooltip.show().addClass('in');
          setTimeout(function() {
            tooltip.removeClass('in');
          }, 2000);
          setTimeout(function() {
            tooltip.hide();
          }, 3000);
          // prevent form submission
          event.preventDefault();
        } else {
          $('.plupload-submit').prop('disabled', true);
          $('#indicator').show();
        }
      });
    }

    // function to display a self-formed error response
    function displayError(file, errormsg) {
      var $entry = $('#pl_'+file.id);

      // If FILE_SIZE_ERROR or plupload.FILE_EXTENSION, this is triggered first, so we
      // build the entry, instead of retrieving it
      if (!$entry.length) {
        // if mixitup not started, let's do it
        var ul = $('#'+ulid);
        if (!ul.hasClass('mixitup')) {
          mixitup.call(ul);
        }
        // then create our entry (empty since it will be filled right after this)
        ul.mixItUp('append', $entry = $('<li class="plupload-entry" id="pl_'+file.id+'"/>'));
      }

      // It is strange if failed images are re-ordered to the beginning, so we rather try to copy
      // the timestamp of previous 'good image' if any
      $entry.attr('data-datetime',
        $entry.prevAll(':has(.plupload-image-container)').first().attr('data-datetime') || '0');

      $entry.html($('<div class="plupload-error"></div>')
        .append($('<div/>')
          .append(
            document.createTextNode(file.name),
            $('<div class="global_form_error"><ul><li>'+errormsg+'</li></ul></div>')
          )
        )
        .append(
          $('<button type="button" class="plupload-close">×</button>')
        )
      );

      // we still need to sort, because we could have a batch of photos where
      // only the last would get an error
      sort();
    }

    function cancelUpload(fileid) {
      uploader.removeFile(uploader.getFile(fileid));
    }

    function removeEntry() {
      var $entry = $(this).closest('.plupload-entry');
      $entry.addClass('remove');
      $('#'+ulid).mixItUp('filter', ':not(.remove)', function() {
        $entry.remove();
        $('.plupload-submit').prop('disabled', !$('.plupload-entry input[name^=name]').length);
      });
    }

    function isValid() {
      var $this = $(this);
      var valid = $this.val().replace(/^\s+|\s+$/g,"").length >= 4;
      $this.attr('data-invalid', valid);
      if (valid) {
        $this.closest('.plupload-entry').removeClass('invalid');
      }
      return valid;
    }

    function titlesValid() {
      var valid = true;
      $('.plupload-entry input[name^=name]').each(function() {
        valid = isValid.call(this) && valid;
      });
      return valid;
    }

    // set tabindex for title inputs
    function setTabindex() {
      $('.plupload-image-entry input[name^=name]').each(function(i) {
        this.tabIndex = i + 1;
      });
    }

    function mixitup() {
      this.mixItUp({
        controls: {
          enable: false
        },
        selectors: {
          target: '.plupload-entry'
        },
        layout: {
          containerClass: 'mixitup',
          containerClassFail: 'empty'
        }
      });
    }

    function sort() {
      // don't sort when images are being uploaded, that would
      // cause strange behaviours
      if (!$('.plupload-loading').length) {
        $('#'+ulid).mixItUp('sort', 'datetime', setTabindex);
      } else {
        setTabindex();
      }
    }

    // resize modalbox when window is resized
    function resize() {
      var width = $(window).width() - 60,
          height = $(window).height() - $('.modal-header').outerHeight(true) -
            $('.plupload-tips').outerHeight(true) - $('.plupload-control').outerHeight(true) -
            $('.modal-footer').outerHeight(true) - parseInt($('.modal-body').css('paddingBottom'), 10)*2 - 30;

      $('#modalbox').width(width)
        .css('margin-left', Math.round(width / -2));
      $('#images_validate_form').height(height);

      uploader.refresh();
    }


    return init(upload_url, backup_url, i18n);
  };

})(window.C2C = window.C2C || {}, jQuery);
