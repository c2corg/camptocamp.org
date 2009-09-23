<?php
use_helper('Form', 'MyImage', 'Button');

// indiquer si etat a completer, erreur ou ok
?>
<?php
echo image_tag(image_url($image_filename, 'small', false, true), array('class' => 'temp'));
echo __('categories (multiple selection allowed)');
?>
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
  ?></div><br />
<?php
echo __('name') . ' ' . input_tag("name[$image_number]"). ' ';
echo input_hidden_tag("image_unique_filename[$image_number]", $image_filename);

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
?>
