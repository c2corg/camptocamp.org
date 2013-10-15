<?php
use_helper('Form', 'MyImage', 'Button', 'Javascript');

echo link_to_function(picto_tag('action_cancel', __('close')),
                                '$(this).up().hide();Modalbox.resizeToContent();',
                                 array('style' => 'float:right;'));
echo image_tag(image_url($image_filename, 'small', false, true), array('class' => 'temp'));
echo __('categories (multiple selection allowed)');
?>
<div class="file_to_upload_categories">
<?php
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
?>
</div>
<br />
<div class="image_form_error"<?php echo isset($image_title) ? ' style="display:none"' : ''?>>
↓&nbsp;<?php echo __('this name is too short (4 characters minimum)') ?> &nbsp;↓</div>
<?php
// if image_title is set, we prefill the title input
// note that we use raw value, since input_tag will escape values anyway
// and we thus prevent double escaping
$image_title = isset($image_title) ? $sf_data->getRaw('image_title') : '';
echo __('name'), ' ',
     input_tag("name[$image_number]", $image_title, array('maxlength' => '150', 'class' => 'large_input',
         'placeholder' => __('write a caption'))),
     ' ', input_hidden_tag("image_unique_filename[$image_number]", $image_filename);

echo '<br /><br />';
$license_choices = array_map('__', sfConfig::get('mod_images_type_list'));
if ($default_license == 1) // collaborative licence is mandatory if it is the one proposed by default
{
    $types = sfConfig::get('mod_images_type_list');
    echo __('image_type') . ' ' . __($types[1]) . '&nbsp;' . link_to(picto_tag('cc-by-sa-mini', 'CC-by-sa'), getMetaArticleRoute('licenses', false, 'cc-by-sa'));
    echo input_hidden_tag("image_type[$image_number]", 1);
}
else
{
    echo __('image_type') . ' ' . select_tag("image_type[$image_number]", options_for_select($license_choices, $default_license));
}
