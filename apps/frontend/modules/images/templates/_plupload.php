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
<div id="container">
<input type="button" value="Select files" id="pickfiles" />
<span id="filelist">No runtime found</span>
<?php
echo form_tag('images/jsupload?mod=' . $mod . '&document_id=' . $document_id, array('id' => 'images_validate_form'));
echo button_to_function(__('save'), "this.hide(); $('images_validate_form').submit()", array('disabled' => 'disabled', 'id' => 'images_submit'));
?>
<div id="files_to_upload">
</div>
</div>
<?php
$backup_url = url_for("@image_jsupload?mod=$mod&document_id=$document_id");
$backup_js = '/'.sfSVN::getHeadRevision('image_upload.js').javascript_path('/static/js/image_upload.js');
echo javascript_tag("
new PeriodicalExecuter(PlUploadWrapper.validateImageForms, 1);
PlUploadWrapper.init('/images/addpltempimage/$mod/$document_id', '$backup_url', '$backup_js');");
?>
</form>
