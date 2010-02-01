<?php
use_helper('Ajax', 'Form', 'Javascript', 'MyForm', 'Escaping');

$validation     = sfConfig::get('app_images_validation');
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
echo form_tag('images/jsupload?mod=' . $sf_params->get('mod') . '&document_id=' . $sf_params->get('document_id'),
              array('multipart' => true, 'name' => 'form_file_input', 'id' => 'form_file_input'));
$js = "if (ImageUpload.submit($('form_file_input'), { 
                                  'onStart' : ImageUpload.startCallback,
                                  'onComplete' : ImageUpload.completeCallback
                              })) {
           $('form_file_input').submit();
       }";
echo input_file_tag('image_file', array('onchange' => $js));
echo input_hidden_tag('action', 'addtempimage');
echo input_hidden_tag('image_number', 0);
?>
</form>
</div>
<?php
echo form_tag('images/jsupload?mod=' . $sf_params->get('mod') . '&document_id=' . $sf_params->get('document_id'), array('id' => 'images_validate_form'));
?>
<div id="files_to_upload">
</div>
<div>
<?php
echo submit_tag(__('Add these images to the document'));
?>
</div>
</form>
