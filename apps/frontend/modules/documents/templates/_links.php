<div id="nav_figures">
    <div id="nav_figures_top"></div>
    <div id="nav_figures_content">
        <div class="link_nav_news"><?php echo __('Toolbox') ?></div>
        <ul>
          <li><?php echo link_to(__('Prepare outing links'), getMetaArticleRoute('prepare_outings')) ?></li>
          <li><?php echo link_to(__('recent conditions'), 'outings/conditions') ?></li>
          <li><?php echo link_to(__('Latest outings'), '@ordered_list?module=outings&orderby=date&order=desc') ?></li>
          <li><?php echo link_to(__('Search a routes'), '@filter?module=routes') ?></li>
        </ul>
    </div>
    <div id="nav_figures_down"></div>
</div>
