(function(C2C, $) {

  C2C.toggle_time = function() {
    $('.absolute_time').toggle();
    $('.relative_time').toggle();
  };

  C2C.toggle_minor_revision = function() {
    $('.minor_revision').toggle();
  };

})(window.C2C = window.C2C || {}, jQuery);
