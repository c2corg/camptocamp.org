<?php use_helper('General', 'Validation', 'Button') ?>
<div class="file_to_upload" id="div_image_<?php echo $image_number ?>">
  <?php echo form_error("image_$image_number"); ?>
  <div class="file_to_upload_button">
  <?php echo link_to_function(picto_tag('picto_rm', __('delete this file')),
                              "$('#div_image_" . $image_number . "').remove()") ?>
  </div>
  <div class="file_to_upload_info"><p><?php
  echo __('File:') . ' ' . input_file_tag("image_file[$image_number]");
  echo '</p><p>';
  echo form_error("name_$image_number");
  echo __('name') . ' ' . input_tag("name[$image_number]", NULL, array('maxlength' => '150'));
  echo '</p><p>';
  $license_choices = array_map('__', sfConfig::get('mod_images_type_list'));
  if ($default_license == 1) // collaborative licence is mandatory if it is the one proposed by default
  {
      $types = sfConfig::get('mod_images_type_list');
      echo __('image_type') . ' ' . __($types[1]) . '&nbsp;' . link_to('<span class="license_mini license_mini_bysa" title="CC-by-sa"></span>', getMetaArticleRoute('licenses', false, 'cc-by-sa'));
      echo input_hidden_tag("image_type[$image_number]", 1);
  }
  else
  {
      echo __('image_type') . ' ' . select_tag("image_type[$image_number]", options_for_select($license_choices, $default_license));
  }
  echo '</p><p class="file_to_upload_categories_title">';
  echo __('categories (multiple selection allowed)');
  ?></p></div>
  <div class="file_to_upload_categories"><?php
  $home_categories = sfConfig::get('app_images_home_categories');
  $choices = array_map('__', sfConfig::get('mod_images_categories_list'));
  foreach($home_categories as $cat)
  {
      if (array_key_exists($cat, $choices))
      {
          $choices[$cat] .= ' *';
      }
  }
  echo select_tag("categories[$image_number]", options_for_select($choices), array('multiple' => true, 'size' => 6));
  ?></div>
</div>
