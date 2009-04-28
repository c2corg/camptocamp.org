<?php use_helper('Button', 'Javascript', 'Home');

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_about">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('about', __('home_welcome'), 'info'); ?>
        <div class="nav_box_text" id="nav_about_section_container">
            <?php echo __('home_description') ?>
            <br />
            <p class="nav_box_bottom_link"><?php echo button_know_more() ?></p>
        </div>
        <?php
        echo javascript_tag("setHomeFolderStatus('nav_about', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
