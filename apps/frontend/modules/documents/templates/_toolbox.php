<?php
if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_toolbox">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('toolbox', __('Toolbox'), 'tools'); ?>
        <div class="nav_box_text" id="nav_toolbox_section_container">
            <ul>
                <li><?php echo link_to(__('recent conditions'), 'outings/conditions') ?></li>
                <li><?php echo link_to(__('Latest outings'), '@ordered_list?module=outings&orderby=date&order=desc') ?></li>
                <li><?php echo link_to(__('Search a routes'), '@filter?module=routes') ?></li>
                <!--<li><a href="http:///">Carte interactive (TODO)</a></li>-->
                <?php if ($sf_user->getCulture() == 'fr'): ?>
                    <li><?php echo link_to(__('New routes article'), getMetaArticleRoute('home_articles', false, 'chroniques-ouvertures')) ?></li>
		<?php endif; ?>
                <li><?php echo link_to(__('Camptocamp-Association'), getMetaArticleRoute('association', false)) ?></li>
                <li><?php echo link_to(__('How to customize'), getMetaArticleRoute('customize', false)) ?></li>
                <li><?php echo link_to(__('Shop'), getMetaArticleRoute('shop', false)) ?></li>
                <li><?php echo link_to(__('Global help'), getMetaArticleRoute('help', false)) ?></li>
                <li><?php echo link_to(__('Guidebook help'), getMetaArticleRoute('help_guide', false)) ?></li>
                <li><?php echo link_to(__('FAQ'), getMetaArticleRoute('faq', false)) ?></li>
            </ul>
        </div>
        <?php
        echo javascript_tag("setHomeFolderStatus('nav_toolbox', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>
