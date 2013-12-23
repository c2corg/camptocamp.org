<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'Escaping', 'MyMinify');

$validation = sfConfig::get('app_images_validation');
?>
<div id="image_upload">
<div id="plupload_tips" class="tips">
<div id="plupload_normal">
<?php echo __('plupload introduction text',
              array('%1%' => implode(', ', $validation['file_extensions']),
                    '%2%' => $validation['max_size']['height'],
                    '%3%' => $validation['max_size']['width'],
                    '%4%' => $validation['weight'] / pow(1024, 2),
                    '%5%' => $validation['min_size']['height'],
                    '%6%' => $validation['min_size']['width']))
?>
</div></div>
<div id="container">
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id, array('id' => 'form_file_input'));
?>
<input type="button" value="<?php echo __('Add images') ?>" id="pickfiles" disabled="disabled" />
<span class="plupload-drag-drop" style="display:none"><?php echo __('or drag & drop files') ?></span>
<?php echo button_to_function(__('save'), "$('.images_submit').hide(); $('#images_validate_form').submit()",
                              array('style' => 'display:none', 'disabled' => 'disabled', 'class' => 'images_submit')); ?>
</form>
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id, array('id' => 'images_validate_form'));
?>
<div id="files_to_upload">
</div>
<div>
<?php echo button_to_function(__('save'), "$('.images_submit').hide(); $('#images_validate_form').submit()",
                              array('style' => 'display:none', 'disabled' => 'disabled', 'class' => 'images_submit')); ?>
</div>
<?php
$plupload_js = minify_get_combined_files_url(array('/static/js/plupload.c2c.js', '/static/js/plupload.wrapper.js'));
$backup_url = url_for("@image_jsupload?mod=$mod&document_id=$document_id");
echo javascript_tag("$.ajax({ url: '$plupload_js', dataType: 'script', cache: true })" .
".done(function() { C2C.PlUploadWrapper.init('/images/addpltempimage/mod/$mod/document_id/$document_id', '$backup_url', {" .
    "badselect: '".__('plupload bad selection')."', extensions: '".__('plupload extension')."', unknownerror: '".__('plupload unknown')."'," .
    "sending: '".__('plupload sending')."', waiting: '".__('plupload waiting')."', serverop: '".__('plupload serverop')."'," .
    "cancel: '".__('cancel')."', drop: '".__('plupload drop')."'" .
  "});" .
"});");
?>
</form>
