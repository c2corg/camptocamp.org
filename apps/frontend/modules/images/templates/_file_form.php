<div class="file_to_upload" id="div_image_<?php echo $image_number ?>">
  <?php echo form_error("image_$image_number"); ?>
  <div class="file_to_upload_button">
  <?php echo link_to_function(image_tag(sfConfig::get('app_static_url') . '/static/images/picto/rm.png',
                              array('alt' => '-', 'title' => __('delete this file'))),
                              "$('div_image_" . $image_number . "').remove()") ?>
  <h2><?php echo $image_number + 1 ?></h2></div>
  <div class="file_to_upload_info"><p><?php
  echo __('File:') . ' ' . input_file_tag("image[$image_number]");
  echo '</p><p>';
  echo form_error("name_$image_number");
  echo __('name') . ' ' . input_tag("name[$image_number]");
  echo '&nbsp;&nbsp;&nbsp;' . __('categories');
  ?></p></div>
  <div class="file_to_upload_categories"><?php
  $choices = array_map('__', sfConfig::get('mod_images_categories_list'));
  echo select_tag("categories[$image_number]", options_for_select($choices), array('multiple' => true, 'size' => 4));
  ?></div>
</div>
