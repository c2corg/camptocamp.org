<div class="image_upload_entry">
<?php
include_partial('images/temp_image_success',
                array('image_filename' => $image_filename,
                      'default_license' => $default_license,
                      'image_number' => $image_number,
                      'image_datetime' => $image_datetime,
                      'image_title' => isset($image_title) ? $image_title : null));
?>
</div>
