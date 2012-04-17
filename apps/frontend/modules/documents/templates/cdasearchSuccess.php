<?php
use_helper('Form', 'FilterForm', 'General', 'MyForm', 'Button');

$lang = $sf_user->getCulture();
$type = $sf_params->get('type');
?>

<div class="column span-6">
  <a href="<?php echo url_for('@cdasearch_by_type?lang=' . $lang . '&type=routes'); ?>">
    <?php
      $img_title = 'Search routes';
      $img = ($type != null) ? '/static/images/cda/slide6a_small.jpg' : '/static/images/cda/slide6a.jpg';
      echo image_tag($img, array('alt'=>__($img_title),'title'=>__($img_title)));
    ?>
    <div class="img_title"><?php echo __($img_title); ?></div>
  </a>
</div>
<div class="column last span-6<?php if ($type != null): echo ' folded'; endif?>">
  <a href="<?php echo url_for('@cdasearch_by_type?lang=' . $lang . '&type=outings'); ?>" >
    <?php
      $img_title = __('Search outings');
      $img = ($type != null) ? '/static/images/cda/slide7a_small.jpg' : '/static/images/cda/slide7a.jpg';
      echo image_tag($img, array('alt'=>__($img_title),'title'=>__($img_title)));
    ?>
    <div class="img_title"><?php echo __($img_title); ?></div>
  </a>
</div>
<?php if ($type != null): ?>
<h2><?php echo __('ici un ttexte pour savoir si iti ou sortie'); ?></h2>
<?php
echo form_tag("/documents/cdaredirect", array('id' => 'filterform')); // FIXME redirect
echo input_hidden_tag('module', $type, array('id' => 'module'));
echo around_selector('arnd') . '<br />';
include_partial('areas/areas_selector', array('ranges' => $ranges, 'use_personalization' => false));

$activities_raw = $sf_data->getRaw('activities');
$paragliding_tag = sfConfig::get('app_tags_paragliding');
$paragliding_tag = implode('/', $paragliding_tag);
echo '<br />' . __('cda_activities') . ' ' . activities_selector(false, true, $activities_raw, array(8 => $paragliding_tag));
echo __('cda_difficulty') . ' ' . select_tag('difficulty', options_for_select(array_map('__', sfConfig::get('app_cda_difficulty'))));
echo __('cda_elevation') . ' ' . select_tag('elevation', options_for_select(array_map('__', sfConfig::get('app_cda_elevation'))));
?>
<br />
<br />
<?php
echo c2c_reset_tag(__('Cancel'), array('picto' => 'action_cancel'));
echo c2c_submit_tag(__('Search on c2c'), array('picto' => 'action_filter', 'class' => 'main_button'));
?>
</form>
<?php endif?>
<div class="fake_clear"> &nbsp;</div>