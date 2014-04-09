// simple dropdown behaviour for our plupload presentation
(function($) {
  var dropdownclass = '.plupload-dropdown';

  function toggle(e) {
    var $this = $(this);

    var isActive = $this.hasClass('open');

    clear(); // make sure that other dropdowns are closed

    if (!isActive) {
      $this.addClass('open').focus().parent().addClass('active');
    }

    return false;
  }

  function clear(e) {
    $(dropdownclass).removeClass('open').parent().removeClass('active');
  }

  $(document)
    .on('click.pl', clear)
    .on('click.pl', dropdownclass, toggle)
    .on('click.pl', dropdownclass + '+ .content.keep-open', function(e) { e.stopPropagation(); });
})(jQuery);
