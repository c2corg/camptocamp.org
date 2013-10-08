<?php use_helper('JavascriptQueue') ?>

<div id="preview" style="display:none;">
</div>

<?php if ($concurrent_edition) {
echo javascript_queue("jQuery.post('" . url_for($sf_context->getModuleName() . "/ViewCurrent?id=$id&lang=$lang") ."')
.done(function(data) {
  var preview = jQuery('#preview');
  preview.html(data).show();
  jQuery('html, body').animate({scrollTop: preview.offset().top - 35}, 2000);
});");

}
