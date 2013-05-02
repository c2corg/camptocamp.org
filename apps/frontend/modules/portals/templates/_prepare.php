<div id="nav_prepare" class="latest">
<?php
use_helper('Javascript');
if (!isset($title))
{
    $title = __('Prepare outing');
}
if (!isset($default_open))
{
    $default_open = true;
}

if (!isset($content_id))
{
    $content_id = 'prepare_outing_box';
}
$html_content = __($content_id);
if ($html_content != 'donotshow'):

include_partial('documents/home_section_title',
                array('module' => 'outings',
                      'has_title_link' => false,
                      'custom_title_text' => $title,
                      'custom_section_id' => 'nav_prepare'));
?>
<div class="home_container_text nav_box_text" id="nav_prepare_section_container">
<?php
    echo $html_content;
?>
    <p class="nav_box_bottom_link"><?php echo link_to(__('More links'), getMetaArticleRoute('prepare_outings')) ?></p>
</div>
<?php
$cookie_position = array_search('nav_prepare', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'nav_prepare\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
<?php
endif;
