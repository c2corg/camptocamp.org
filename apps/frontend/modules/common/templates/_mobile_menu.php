<?php
use_helper('General', 'Forum');

$menu_search = array(
    '@filter?module=outings' => __('outings'),
    '@filter?module=routes' => __('routes'),
    '@filter?module=images' => __('images'),
    '@filter?module=summits' => __('summits'),
    '@filter?module=sites' => __('sites'),
    '@filter?module=parkings' => __('parkings'),
    '@filter?module=huts' => __('huts'),
    '@filter?module=articles' => __('articles'),
    '@filter?module=areas' => __('areas'),
    '@filter?module=maps' => __('maps'),
    '@filter?module=products' => __('products'),
    '@filter?module=users' => __('users'),
);

$menu_see = array(
    '@default_index?module=outings&orderby=date&order=desc' => __('outings'),
    '@default_index?module=routes' => __('routes'),
    '@default_index?module=images' => __('images'),
    '@default_index?module=summits' => __('summits'),
    '@default_index?module=sites' => __('sites'),
    '@default_index?module=parings' => __('parkings'),
    '@default_index?module=huts' => __('huts'),
    '@default_index?module=articles' => __('articles'),
    '@default_index?module=areas' => __('areas'),
    '@default_index?module=maps' => __('maps'),
    '@default_index?module=products' => __('products'),
    '@default_index?module=users' => __('users')
);

$menu_more = array(
    getMetaArticleRoute('association') => __('Association'),
    getMetaArticleRoute('help', false) => __('Global help'),
    getMetaArticleRoute('home_guide') => __('Help').__(' :').' '.__('Guidebook'),
    getMetaArticleRoute('help_forum', false) => __('Help').__(' :').' '.__('Forums')
);
?>
<div id="mobile_menu">
  <div id="menu_items">
    <div class="menu_entry">
      <div class="menu_item left">
        <?php echo f_link_to('<span class="select_button">' . __('Forums') . '</span>',
                           '?lang='.$lang); ?>
      </div>
    </div>
    <div class="menu_entry middle">
      <div class="menu_item">
        <span class="select_button"><?php echo __('Search') ?></span>
        <?php echo select_tag('menu_select', options_for_select($menu_search), array('class' => 'menu_select', 'id' => 'menu_select1')) ?>
      </div>
    </div>
    <div class="menu_entry">
      <div class="menu_item middle">
        <span class="select_button"><?php echo __('See') ?></span>
        <?php echo select_tag('menu_select', options_for_select($menu_see), array('class' => 'menu_select', 'id' => 'menu_select2')) ?>
      </div>
    </div>
    <div class="menu_entry">
      <div class="menu_item right">
        <span class="select_button"><?php echo __('More...') ?></span>
        <?php echo select_tag('menu_select', options_for_select($menu_more), array('class' => 'menu_select', 'id' => 'menu_select3')) ?>
      </div>
    </div>
  </div>
</div>
