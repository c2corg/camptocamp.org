<?php
if (!isset($open))
{
    $open = true;
}
?>
<div id="nav_figures">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('figures',  __('Camptocamp.org is about:'), 'info', $open); ?>
        <div class="nav_box_text" id="nav_figures_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <ul>
            <?php foreach ($figures as $type => $nb): ?>
                <li><?php echo $nb . ' ' . __($type) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>