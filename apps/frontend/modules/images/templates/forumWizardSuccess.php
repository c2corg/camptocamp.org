<?php
// this file would be more logically in webforums, but symfony helpers are very useful
?>
<div id="images_wizard">
  <div id="images_wizard_ondrag" style="z-index:-1;"><p><?php echo __('plupload drop') ?></p></div>
  <div><?php echo __('forum images wizard info %%1%% or drag and drop',
    array('%%1%%' => '<input id="images_wizard_select" type="button" value="'.__('Select image on computer').'" onclick="$(\'#images_wizard_file\').click()" />')) ?>
  </div>
  <div>
    <label><?php echo __('Image title') ?> <input id="images_wizard_caption" class="large_input" type="text" /></label>
    <label><?php echo __('Image url') ?> <input id="images_wizard_url" class="large_input" type="text" value="" placeholder="http://" /></label>
    <input id="images_wizard_file" type="file" onchange="C2C.upload_local_file(this.files);" style="display:none" />
  </div>
  <br />
  <div>
    <input type="button" value="<?php echo __('Insert') ?>" onclick="C2C.insert_image_code()" />
    &nbsp;&nbsp;
    <input type="button" value="<?php echo __('Enter code manually') ?>" onclick="C2C.insert_text('[img]', '[/img]'); C2C.close_images_wizard()" />
    <input type="button" value="<?php echo __('Cancel') ?>" onclick="C2C.close_images_wizard()" />
  </div>
</div>
<script>
(function() {
  var div = $('#images_wizard'), dragdiv = $('#images_wizard_ondrag'), dragging = 'dragging';
  dragdiv.height(div.height() - 12).width(div.width() - 12);
  $(document).on('dragenter', function(e) {
    div.addClass(dragging);
    dragdiv.css('zIndex', 1);
  }).on('mouseout', function(e) {
    div.removeClass(dragging);
    dragdiv.css('zIndex', -1);
  });
  div.on('dragenter dragover', function(e) {
    e.stopPropagation();
    e.preventDefault();
  }).on('drop', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).removeClass(dragging);
    C2C.upload_local_file(e.originalEvent.dataTransfer.files);
  });
})();
</script>
