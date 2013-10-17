(function(C2C, $) {

  var mobile = $('html').hasClass('mobile');

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
    div.show();

    if (!mobile) {
      div.delay(3000)
        .animate({
          opacity: 0
        }, {
          duration: 1500,
          complete: function() {
            $(this).hide().css('opacity', 1);
          }
        });
    } else {
      div.click(function() {
        $(this).hide();
      });
    }
  }

})(window.C2C = window.C2C || {}, jQuery);
