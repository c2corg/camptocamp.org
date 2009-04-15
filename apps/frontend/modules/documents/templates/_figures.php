<?php
if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_figures">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('figures',  __('Camptocamp.org is about:'), 'info'); ?>
        <div class="nav_box_text" id="nav_figures_section_container">
            <ul>
            <?php foreach ($figures as $type => $nb): ?>
                <li><?php echo $nb . ' ' . __($type) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php
        echo javascript_tag("setHomeFolderStatus('nav_figures', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
