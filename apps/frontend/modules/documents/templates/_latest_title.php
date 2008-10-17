<?php
$tr_module =  __($module);
$lang = $sf_user->getCulture();
$static_base_url = sfConfig::get('app_static_url');
?>
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo image_tag($static_base_url . '/static/images/modules/' . $module . '_mini.png',
               array('alt' => $tr_module, 'title' => $tr_module)) . ' ';
echo link_to(__("Latest $module"), "@default_index?module=$module");
?>
</span><span class="home_title_right">
<?php
echo link_to(image_tag($static_base_url . '/static/images/picto/rss.png',
                       array('alt' => __('RSS feed creations'))),
             "@creations_feed?module=$module&lang=$lang",
             array('title' => __("Subscribe to latest $module creations")));
?>
</span></div>
