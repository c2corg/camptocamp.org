(function(C2C) {

  C2C.tog = function() {
    $$('.absolute_time').invoke('toggle');
    $$('.relative_time').invoke('toggle');
  };

  C2C.toggle_minor_revision = function() {
    $$('.minor_revision').invoke('toggle');
  };

})(window.C2C = window.C2C || {});
