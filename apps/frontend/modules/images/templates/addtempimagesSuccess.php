<?php
$images = array_reverse($sf_data->getRaw('images'));
foreach ($images as $image):
?>
<div class="image_upload_entry">
<?php
    if (isset($image['error']))
    {
        // FIXME bad trick in order not to rewrite the global_form_errors_tag function
        sfContext::getInstance()->getRequest()->setError($image['error']['field'], $image['error']['msg']);
        include_partial('images/temp_image_error',
                        array('image_name' => $image['image_name']));
        sfContext::getInstance()->getRequest()->removeError($image['error']['field']);
    }
    else
    {
        include_partial('images/temp_image_success',
                        array('image_filename' => $image['image_filename'],
                              'default_license' => $image['default_license'],
                              'image_number' => $image['image_number'],
                              'image_datetime' => $image['image_datetime'],
                              'image_title' => isset($image['image_title']) ? $image['image_title'] : null)
                        );
    }
?>
</div>
<?php
endforeach;
