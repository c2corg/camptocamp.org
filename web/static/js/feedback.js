(function(C2C, $) {
  
  C2C.showFailure = function(text) {
    showFeedback('failure', text);
  };

  C2C.showSuccess = function(text) {
    showFeedback('success', text)
  }

  function showFeedback(type, text) {
    var div = (type == 'success') ? $('#ajax_feedback_success') : $('#ajax_feedback_failure');
    if (text) {
      div.html(text);
    }
    div
      .show()
      .delay(3000)
      .animate({
        opacity: 0
      }, {
        duration: 1500,
        complete: function() {
          $(this).hide().css('opacity', 1);
        }
      });
  }

})(window.C2C = window.C2C || {}, jQuery);
