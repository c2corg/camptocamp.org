<?php
if (!isset($open))
{
    $open = true;
}
?>
<div id="nav_toolbox">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('toolbox', __('Toolbox'), 'tools', $open); ?>
        <div class="nav_box_text" id="nav_toolbox_section_container" <?php if (!$open) echo 'style="display: none;"'; ?>>
            <ul>
                <li><?php echo link_to(__('recent conditions'), 'outings/conditions') ?></li>
                <li><?php echo link_to(__('Latest outings'), '@ordered_list?module=outings&orderby=date&order=desc') ?></li>
                <li><?php echo link_to(__('Search a routes'), '@filter?module=routes') ?></li>
                <li><a href="http:///">Carte interactive (TODO)</a></li>
                <li><a href="http:///">Chronique des ouvertures (TODO)</a></li>
                <li><?php echo link_to(__('Camptocamp-Association'), getMetaArticleRoute('association')) ?></li>
                <li><?php echo link_to(__('How to customize'), getMetaArticleRoute('customize')) ?></li>
                <li><a href="http://camptocamp.shirtcity.com/">Gadgets c2c (TODO)</a></li>
                <li><?php echo link_to(__('Global help'), getMetaArticleRoute('help')) ?></li>
                <li><?php echo link_to(__('Guidebook help'), getMetaArticleRoute('help_guide')) ?></li>
            </ul>
        </div>
    </div>
    <div class="nav_box_down"></div>
</div>