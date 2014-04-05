<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'Escaping', 'MyMinify');

$validation = sfConfig::get('app_images_validation');
?>
<div id="image_upload">
<div class="tips plupload-tips">
<?php echo __('plupload introduction text',
              array('%1%' => implode(', ', $validation['file_extensions']),
                    '%2%' => $validation['max_size']['height'],
                    '%3%' => $validation['max_size']['width'],
                    '%4%' => $validation['weight'] / pow(1024, 2),
                    '%5%' => $validation['min_size']['height'],
                    '%6%' => $validation['min_size']['width'])); ?>
</div>
<div id="plupload-container">
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id, array('id' => 'images_validate_form'/*, 'onsubmit' => "$('#indicator').show();"*/));
?>
<ul id="files_to_upload" class="empty">
  <li class="plupload-indication" style="display:none">
    <div>
      <input type="button" class="plupload-pickfiles" value="<?php echo __('Add images') ?>" onclick="$('#pickfiles').click()" disabled="disabled" />
      <span class="plupload-drag-drop"><?php echo __('or drag & drop files') ?></span>
    </div>
  </li>
</ul>
<div class="plupload-control">
<input id="pickfiles" class="plupload-pickfiles" type="button" value="<?php echo __('Add images') ?>" disabled="disabled" />
<input class="plupload-cancel" type="button" value="<?php echo __('cancel') ?>" />
<input class="plupload-submit" type="submit" value="<?php echo __('save') ?>" disabled="disabled" />
<div class="tooltip top" style="display:none">
  <div class="tooltip-inner"><?php echo __('this name is too short (4 characters minimum)') ?></div>
  <div class="tooltip-arrow"></div>
</div>
</div>
<?php
$plupload_js = minify_get_combined_files_url(array('/static/js/mixitup.js', '/static/js/plupload.c2c.js', '/static/js/plupload.wrapper.js'));
$backup_url = url_for("@image_jsupload?mod=$mod&document_id=$document_id?noplupload=true");
echo javascript_queue(
// compute the height that files_to_upload should take // TODO resize
"var height = $(window).height() - $('.modal-header').outerHeight(true) - $('.plupload-tips').outerHeight(true)" .
"- $('#plupload-container').outerHeight(true) - $('.modal-footer').outerHeight(true)" .
"- parseInt($('.modal-body').css('paddingBottom'), 10)*2 - 30;" .
"$('#images_validate_form').height(height);" .
// load plupload script and init once ready
"$.ajax({ url: '$plupload_js', dataType: 'script', cache: true })" .
".done(function() { C2C.PlUploadWrapper('/images/addpltempimage/mod/$mod/document_id/$document_id', '$backup_url', {" .
    "badselect: '".__('plupload bad selection')."', extensions: '".__('plupload extension')."', unknownerror: '".__('plupload unknown')."'," .
    "sending: '".__('plupload sending')."', waiting: '".__('plupload waiting')."', serverop: '".__('plupload serverop')."'," .
    "cancel: '".__('cancel')."', drop: '".__('plupload drop')."'" .
  "});" .
"});");
?>
</form>
