<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'Escaping');

$validation = sfConfig::get('app_images_validation');
?>
<div id="image_upload">
<p class="tips">
<?php
echo __('You can add %1%, with %3% x %2% px and %4% mo',
              array('%1%' => implode(', ', $validation['file_extensions']),
                    '%2%' => $validation['max_size']['height'],
                    '%3%' => $validation['max_size']['width'],
                    '%4%' => $validation['weight'] / pow(1024, 2)))
    . ' ' .
    __('Minsize is %1% x %2%', array('%1%' => $validation['min_size']['height'], '%2%' => $validation['min_size']['width']));
?>
</p>
<div id="image_input">
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id,
              array('multipart' => true, 'name' => 'form_file_input', 'id' => 'form_file_input'));
?>
<div id="image_selection">
<div class="image_form_error" style="display:none">
↓&nbsp;<?php echo __('wrong file type') ?> &nbsp;↓</div>
<?php
echo label_for('image_file', __('select an image file'));
echo input_file_tag('image_file[]', array('onchange' => 'C2C.ImageUpload.onchangeCallback()', 'multiple' => 'multiple'));
echo '&nbsp;&nbsp;';
echo button_to_function(__('save'), "jQuery('.images_submit').hide(); jQuery('#images_validate_form').submit()", array('disabled' => 'disabled', 'class' => 'images_submit'));
echo input_hidden_tag('action', 'addtempimages');
echo input_hidden_tag('image_number', 0);
?>
<span id="image_add_str" style="display:none">
<?php echo __('add an other image'); ?>
</span>
</div>
</form>
</div>
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id, array('id' => 'images_validate_form'));
?>
<div>
</div>
<div id="files_to_upload">
</div>
<div>
<?php echo button_to_function(__('save'), "jQuery('.images_submit').hide(); jQuery('#images_validate_form').submit()", array('disabled' => 'disabled', 'class' => 'images_submit')); ?>
</div>
<?php
echo javascript_tag('C2C.ImageUpload.init()');
?>
</form>
