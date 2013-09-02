/* =========================================================
 * this modalbox implementation is derived from twitter bootstrap,
 * licensed un the Apache License, Version 2.0
 * http://twitter.github.com/bootstrap/javascript.html#modals
 * Copyright 2012 Twitter, Inc.
 * ========================================================= */

(function ($) {

  var Modal = function(options) {
    this.options = options;

    // Adapt style if width is set as an option
    if (this.options.width) {
      $('.modal').width(this.options.width)
        .css('margin-left', Math.round(this.options.width / -2));
    } else {
      $('.modal').removeAttr('style');
    }

    $('.modal-backdrop').toggle(!!this.options.backdrop);

    this.$element = $('#modalbox')
      .on('click.dismiss.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this));

    this.$element.find('.modal-header h3').text(this.options.title || '');

    if (this.options.remote) {
      this.$element.find('.modal-body')
        .append('<div class="modal-loading"/>')
        .load(this.options.remote, function() {
          // set focus on input autofocus if it exists
          $(this).find('input[autofocus]').focus();
        });
    } // if no remote option, we display the box as is
  };

  Modal.prototype = {

    constructor: Modal,

    toggle: function() {
      return this[!this.isShown ? 'show' : 'hide']();
    },

    show: function() {
      this.isShown = true;
      this.escape();
      if (!!this.options.backdrop && this.options.backdrop != 'static') {
        $('.modal-backdrop').on('click.dismiss.modal', $.proxy(this.hide, this));
      }
      this.$element.addClass('in');
    },

    hide: function() {
      this.isShown = false;
      this.escape();
      $('.modal-backdrop').off('click.dismiss.modal');
      this.$element.removeClass('in');
    },

    // set escape key
    escape: function () {
      var that = this;
      if (this.isShown && this.options.keyboard) {
        $(document).on('keyup.dismiss.modal', function(e) {
          if (e.which == 27) that.hide();
        });
      } else if (!this.isShown) {
        $(document).off('keyup.dismiss.modal');
      }
    }
  };

  $.modalbox =  {
    show: function(options) {
      var data;
      var defaults = {
        backdrop: 'static',
        keyboard: true
      };

      options = $.extend({}, defaults, typeof options == 'object' && options);

      $('#modalbox').data('modal', (data = new Modal(options)));
      data.show();
    },

    hide: function() {
      var data = $('#modalbox').data('modal');
      if (data) data.hide();
    }
  };

  // build DOM once
  $('body')
    .append($('<div id="modalbox" class="modal" role="dialog" aria-hidden="true"/>')
      .append($('<div class="modal-content"/>')
        .append('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h3/></div>',
                '<div class="modal-body"/>',
                '<div class="modal-footer"/>')))
    .append('<div class="modal-backdrop"/>');

})(window.jQuery);
