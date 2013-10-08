(function(C2C, $) {

  var indicator = $('#indicator');
  var tooltip = $('<div/>', {
    id: 'fields_tooltip',
    'class': 'ajax_feedback',
    style: 'display: none'
  }).click(function() {
    $(this).hide();
  });

  $('body').append(tooltip);

  $('[data-tooltip]').click(function() {
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

})(window.C2C = window.C2C || {}, jQuery);
