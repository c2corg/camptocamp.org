<div id="nav_about" class="latest">
<?php
use_helper('Text', 'sfBBCode', 'SmartFormat', 'SmartDate', 'Button');
if (!isset($title))
{
    $title = __('home_welcome');
}
if (isset($description))
{
    $description = $sf_data->getRaw('description');
}
else
{
    $description = __('home_description');
}
if (!isset($default_open))
{
    $default_open = true;
}

include_partial('documents/home_section_title',
                array('module' => 'info',
                      'has_title_link' => false,
                      'custom_title_text' => $title,
                      'custom_section_id' => 'nav_about'));
?>
<div class="home_container_text nav_box_text" id="nav_about_section_container">
<?php
echo $description;
if (isset($know_more_link)):
    $know_more_link = $sf_data->getRaw('know_more_link');
    if (!isset($know_more_text))
    {
        $know_more_text = __('Know more');
    }
    $link = link_to($know_more_text, $know_more_link);
?>
    <p class="nav_box_bottom_link"><?php echo $link ?></p>
<?php
endif;
?>
</div>
<?php
$cookie_position = array_search('nav_about', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setHomeFolderStatus(\'nav_about\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
