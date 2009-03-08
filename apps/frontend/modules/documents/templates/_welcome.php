<?php use_helper('Button', 'Home');

if (!isset($open))
{
    $open = true;
}
?>
<div id="nav_about">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('about', __('home_welcome'), 'info', $open); ?>
        <div class="nav_box_text" id="nav_about_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <?php echo __('home_description') ?>
            <br />
            <p class="nav_box_bottom_link"><?php echo button_know_more() ?></p>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>
