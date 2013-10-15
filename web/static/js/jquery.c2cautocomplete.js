// this jquery plugin aims at being imore or less compatible with the use of prototype's Ajax.Autocompleter within c2c
// We might use a better one at some point, but that will also require server side work

// heavily inspired and adapted from https://github.com/devbridge/jQuery-Autocomplete

(function($, window, document) {

  // Create the defaults once
  var pluginName = "c2cAutocomplete",
      defaults = {
        paramName: '', // defaults to input's name
        containerClass: 'auto_complete',
        selectedClass: 'selected',
        deferRequestBy: 300, // defer lookup when value changes quickly
        minChars: 3, // minimum number of characters that must be entered before an ajax request is made
        indicator: 'indicator', // html id of an element to display while the ajax request is in progress
        params: {}, // additional parameters; in format field=value&another=value or as an object
        onSelect: null, // callback to be fired once an entry has been selected
        getService: null // you can specifu your own way to retrieve suggestions (defaults to ajax request), using the promise interface
      },
      keys = {
        ESC: 27,
        TAB: 9,
        RETURN: 13,
        UP: 38,
        RIGHT: 39,
        DOWN: 40
      };

  function Plugin(element, options) {
    this.element = element;
    this.el = $(element);
    this.options = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;

    // shared variables
    this.onChangeInterval = null;
    this.currentValue = this.element.value;
    this.suggestionsContainer = null;

    this.init();
  }

  Plugin.prototype = {
    killerFn: null,

    init: function() {
      var that = this,
          selected = that.options.selectedClass;

      that.options.paramName = that.options.paramName || that.el.attr('name');

      // Remove autocomplete attribute to prevent native suggestions:
      that.element.setAttribute('autocomplete', 'off');

      that.killerFn = function(e) {
        if ($(e.target).closest('.' + that.options.containerClass).length === 0) {
          that.killSuggestions();
          that.disableKillerFn();
        }
      };

      that.suggestionsContainer = $('<div/>', {
        'class': that.options.containerClass,
        style: 'position: absolute; display: none;'
      });

      container = $(that.suggestionsContainer);

      container.appendTo('body');

      // Listen for mouse over event on suggestions list
      container.on('mouseover.autocomplete', 'li', function () {
        that.activate($(this).index());
      });

      // Deselect active element when mouse leaves suggestions container
      container.on('mouseout.autocomplete', function () {
        that.selectedIndex = -1;
        container.children('.' + selected).removeClass(selected);
      });

      // Listen for click event on suggestions list
      container.on('click.autocomplete', 'li', function () {
        that.select($(this));
      });

      that.fixPosition();

      that.fixPositionCapture = function () {
        if (that.visible) {
          that.fixPosition();
        }
      };

      $(window).on('resize', that.fixPositionCapture);

      that.el.on('keydown.autocomplete', function(e) { that.onKeyPress(e); });
      that.el.on('keyup.autocomplete', function(e) { that.onKeyUp(e); });
      that.el.on('blur.autocomplete', function(e) { that.onBlur(e); });
      that.el.on('focus.autocomplete', function(e) { that.fixPosition(); });
      that.el.on('change.autocomplete', function(e) { that.onKeyUp(e); });
    },

    fixPosition: function(e) {
      var that = this,
          offset;

      offset = that.el.offset();
      that.suggestionsContainer.css({
        top: (offset.top + that.el.outerHeight()),
        left: offset.left,
        width: that.element.offsetWidth
      });
    },

    enableKillerFn: function() {
      var that = this;
      $(document).on('click.autocomplete', that.killerFn);
    },

    disableKillerFn: function() {
      var that = this;
      $(document).off('click.autocomplete', that.killerFn);
    },

    killSuggestions: function () {
      var that = this;
      that.stopKillSuggestions();
      that.intervalId = window.setInterval(function () {
        that.hide();
        that.stopKillSuggestions();
      }, 300);
    },

    stopKillSuggestions: function() {
      window.clearInterval(this.intervalId);
    },

    onKeyPress: function(e) {
      var that = this;

      // If suggestions are hidden and user presses arrow down or right, display suggestions
      if (!that.visible && that.currentValue &&
          (e.which == keys.DOWN || (e.which == keys.RIGHT && that.isCursorAtEnd()))) {
        that.onValueChange();
        return;
      }

      if (!that.visible) return;

      switch (e.which) {
        case keys.ESC:
          that.el.val(that.currentValue);
          that.hide();
          break;
        case keys.RETURN:
          if (that.selectedIndex === -1) {
            that.hide();
            break; // initially return, but we don't want the enter key to cause form submission
          }
          that.select(that.suggestionsContainer.find('ul > li').eq(that.selectedIndex));
          break;
        case keys.TAB:
        case keys.UP:
          that.move(-1);
          break;
        case keys.DOWN:
          that.move(1);
          break;
        default:
          return;
      }

      // Cancel event if function did not return:
      e.stopImmediatePropagation();
      e.preventDefault();
    },

    onKeyUp: function(e) {
      var that = this;

      switch(e.which) {
        case keys.UP:
        case keys.DOWN:
          return;
      }

      clearInterval(that.onChangeInterval);

      if (that.currentValue !== that.el.val()) {
        if (that.options.deferRequestBy > 0) {
          that.onChangeInterval = setInterval(function () {
            that.onValueChange();
          }, that.options.deferRequestBy);
        } else {
          that.onValueChange();
        }
      }
    },

    isCursorAtEnd: function() {
      var that = this,
          valLength = that.el.val().length,
          selectionStart = that.element.selectionStart,
          range;

      if (typeof selectionStart === 'number') {
        return selectionStart === valLength;
      }
      if (document.selection) {
        range = document.selection.createRange();
        range.moveStart('character', -valLength);
        return valLength === range.text.length;
      }
      return true;
    },

    onValueChange: function() {
      var that = this, q;

      clearInterval(that.onChangeInterval);
      that.currentValue = that.el.val();
 
      that.selectedIndex = -1;

      q = $.trim(that.currentValue);

      if (q.length < that.options.minChars) {
        that.hide();
      } else {
        that.getSuggestions(q);
      }
    },

    onBlur: function(e) {
      this.enableKillerFn();
    },

    getSuggestions: function(q) {
      var that = this,
          options = that.options,
          indicator = $('#'+options.indicator);

      that.fixPosition(); // needed for mobile version

      if ($.isPlainObject(options.params)) {
        options.params[options.paramName] = q;
      } else {
        options.params = options.paramName + '=' + q + '&' + options.params;
      }

      indicator.show();

      $.when(options.getService ? options.getService.call(that, q) : $.get(options.url, options.params))
      .always(function() {
        indicator.hide();
        that.visible = true;
      }).done(function(data) {
        // Display suggestions only if returned query matches current value
        if (q == $.trim(that.currentValue)) {
          that.suggestionsContainer.html(data).show();
        }
      }).fail(function(data) {
        that.suggestionsContainer.html(data.responseText).show();
      });
    },

    move: function(delta) {
      var that = this,
          newIndex = that.selectedIndex + delta;

      if (newIndex < 0 || newIndex >= that.suggestionsContainer.find('ul > li').length) {
        return;
      }

      that.activate(newIndex);
    },

    activate: function(index) {
      var that = this,
          selected = that.options.selectedClass,
          container = $(that.suggestionsContainer),
          children = container.find('ul > li');

      children.filter('.' + selected).removeClass(selected);

      that.selectedIndex = index;

      if (that.selectedIndex !== -1 && children.length > that.selectedIndex) {
        return $(children.get(that.selectedIndex)).addClass(selected);
      }

      return null;
    },

    select: function(selected) {
      var that = this,
          onSelectCallback = that.options.onSelect,
          text = selected.contents().filter(function() {
            return this.nodeType === 3 || !$(this).hasClass('informal');
          }).text();
      that.hide();

      that.currentValue = text;

      that.el.val(text);

      // TODO rather use trigger?
      if ($.isFunction(onSelectCallback)) {
        onSelectCallback.call(selected.get(0));
      }
    },

    hide: function() {
      var that = this;

      that.visible = false;
      that.selectedInex = -1;
      that.suggestionsContainer.hide();
    }
  };

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  // TODO maybe we shoulddestroy old instance and create a new one with new options on new call
  $.fn[pluginName] = function(options) {
    return this.each(function() {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName, new Plugin(this, options));
      }
    });
  };

})(jQuery, window, document);
