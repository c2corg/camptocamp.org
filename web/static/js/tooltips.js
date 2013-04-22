(function(C2C, $) {

  C2C.add_tooltips = function(css_class_to_observe) {
    // once dom loaded, load tooltip info via an ajax request
    // when clicking the field label
    $(function() {
      $(css_class_to_observe).click(function() {

        var indicator = $('#indicator');
        var tooltip = $('#fields_tooltip');

        tooltip.hide();
        indicator.show();

        $.post('/common/getinfo', {
          elt: this.id
        }).done(function(data) {
          indicator.hide();
          tooltip.html(data);
          tooltip.show();
        }).fail(function() {
          indicator.hide();
        });

      });
    });
  };

})(window.C2C = window.C2C || {}, jQuery);
