<?php
use_helper('Form', 'MyImage', 'Button', 'Javascript');
$mobile = c2cTools::mobileVersion();

// use image datetime for sorting images on client side
$tag_params =  array('class' => 'plupload-image');
if (isset($image_datetime))
{
    $tag_params['data-datetime'] = $image_datetime;
}

// image license
$image_types = sfConfig::get('mod_images_type_list');
$license_choices = array_map('__', $image_types);

// categories
$home_categories = sfConfig::get('app_images_home_categories');
$choices = array_map('__', sfConfig::get('mod_images_categories_list'));
foreach($home_categories as $cat)
{
    if (array_key_exists($cat, $choices))
    {
        $choices[$cat] .= ' *';
    }
}
?>

<div class="plupload-image-container">
  <div class="plupload-image-wrapper">
  <?php echo image_tag(image_url($image_filename, 'medium', false, true), $tag_params); ?>
  </div>
  <div class="plupload-image-overlay">
    <?php if ($default_license == 1): // collaborative licence is mandatory if it is the one proposed by default ?>
    <span class="plupload-licence picto cc-by-sa-mini" title="<?php echo __('image_type').' '.__($image_types[1]); ?>"></span>
    <?php echo input_hidden_tag("image_type[$image_number]", 1); ?>
    <?php else: ?>
    <span class="plupload-licence picto cc-by-nc-nd-mini"></span>
    <?php endif; ?>
    <button type="button" class="plupload-close">×</button>
  </div>
</div>
<div class="plupload-image-bottom<?php if ($default_license != 1) echo ' with-licence'; ?>">
  <div class="plupload-image-title">
  <?php
  // if image_title is set, we prefill the title input
  // note that we use raw value, since input_tag will escape values anyway
  // and we thus prevent double escaping
  $image_title = isset($image_title) ? $sf_data->getRaw('image_title') : '';
  echo input_tag("name[$image_number]", $image_title,
                 array('placeholder' => __('write a caption'), 'autocomplete' => 'off', 'maxlength' => 150)),
       input_hidden_tag("image_unique_filename[$image_number]", $image_filename); ?>
  </div>
  <?php if ($default_license != 1): ?>
    <?php if ($mobile): ?>
    <div class="plupload-licence-button" title="<?php echo __('image_type'); ?>">
    <?php echo select_tag("image_type[$image_number]", options_for_select($license_choices, $default_license)); ?>
    </div>
    <?php else: ?>
    <div class="plupload-dropdown-container">
      <div class="plupload-dropdown plupload-licence-button" title="<?php echo __('image_type'); ?>"></div>
      <!--[if IE 8]><div class="content keep-open"><ul><![endif]-->
      <!--[if !IE]> --><div class="content"><ul><!-- <![endif]-->
        <li><label><input name="image_type[<?php echo $image_number; ?>]" type="radio" value="1" /><?php echo $license_choices[1]; ?></label></li>
        <li><label class="checked"><input name="image_type[<?php echo $image_number; ?>]" type="radio" value="2" checked /><?php echo $license_choices[2]; ?></label></li>
      </ul></div>
    </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($mobile): ?>
  <div class="plupload-categories" title="<?php echo __('categories (multiple selection allowed)'); ?>">
    <span class="count">0</span>
    <?php echo select_tag("categories[$image_number]", options_for_select($choices), array('multiple' => true)); ?>
  </div>
  <?php else: ?>
  <div class="plupload-dropdown-container">
    <div class="plupload-dropdown plupload-categories" title="<?php echo __('categories (multiple selection allowed)'); ?>">
      <span class="count">0</span>
    </div>
    <div class="content keep-open"><ul>
      <?php foreach($choices as $value => $name): ?>
      <li><label><input name="categories[<?php echo $image_number; ?>][]" type="checkbox" value="<?php echo $value; ?>" /><?php echo $name; ?></label></li>
      <?php endforeach; ?>
    </ul></div>
  </div>
  <?php endif; ?>
</div>
