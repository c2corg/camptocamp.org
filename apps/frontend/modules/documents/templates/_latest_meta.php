<div id="on_the_web" class="latest">
<?php
use_helper('SmartDate', 'Javascript');

$response = sfContext::getInstance()->getResponse();
$response->addJavascript('/static/js/metac2crss.js');

if (!isset($default_open))
{
    $default_open = true;
}
$tr_module =  __('meta outings');
include_partial('documents/home_section_title',
                array('module'            => 'on_the_web',
                      'custom_section_id' => 'on_the_web',
                      'custom_title'      => link_to(__('Latest outings from MetaEngine'),
                                                     sfConfig::get('app_meta_engine_base_url')),
                      'custom_rss'        => link_to('',
                                                     sfConfig::get('app_meta_engine_base_url') . 'outings',
                                                     array('class' => 'home_title_right picto_rss',
                                                           'title' => __('Subscribe to latest outings from MetaEngine'))),
                      'custom_title_icon' => 'outings'));
?>
<?php
echo javascript_tag('
(function(C2C) {
  C2C.meta_feed_url = "' . html_entity_decode(html_entity_decode($feed_url)) . '";
})(window.C2C = window.C2C || {});
');
?>
<div id="on_the_web_section_container" class="home_container_text">
<ul id="on_the_web_section_list" class="dated_changes">
<li><?php echo __('No recent changes available') ?></li>
</ul>
<div class="home_link_list">
<?php echo link_to('meta.camptocamp.org', sfConfig::get('app_meta_engine_base_url')) ?>
</div>
</div>
<?php
$cookie_position = array_search('on_the_web', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'on_the_web\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
