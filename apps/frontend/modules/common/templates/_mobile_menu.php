<?php
use_helper('General', 'Forum', 'JavascriptQueue');

$menu_search = array(
    '#' => ' ',
    url_for('@filter?module=outings') => __('outings'),
    url_for('@filter?module=routes') => __('routes'),
    url_for('@filter?module=images') => __('images'),
    url_for('@filter?module=summits') => __('summits'),
    url_for('@filter?module=sites') => __('sites'),
    url_for('@filter?module=parkings') => __('parkings'),
    url_for('@filter?module=huts') => __('huts'),
    url_for('@filter?module=books') => __('books'),
    url_for('@filter?module=articles') => __('articles'),
    url_for('@filter?module=products') => __('products'),
    url_for('@filter?module=users') => __('users'),
);

$menu_see = array(
    '#' => ' ',
    url_for('@default_index?module=outings&orderby=date&order=desc') => __('outings'),
    url_for('@default?module=outings&action=conditions&orderby=date&order=desc') =>  __('cond short'),
    url_for('@default_index?module=routes') => __('routes'),
    url_for('@default_index?module=images') => __('images'),
    url_for('@default_index?module=summits') => __('summits'),
    url_for('@default_index?module=sites') => __('sites'),
    url_for('@default_index?module=parkings') => __('parkings'),
    url_for('@default_index?module=huts') => __('huts'),
    url_for('@default_index?module=books') => __('books'),
    url_for('@default_index?module=articles') => __('articles'),
    url_for('@default_index?module=products') => __('products'),
    url_for('@default_index?module=users') => __('users')
);

$menu_more = array(
    '#' => ' ',
    url_for(getMetaArticleRoute('association')) => __('Association'),
    url_for(getMetaArticleRoute('help', false)) => __('Global help'),
    url_for(getMetaArticleRoute('home_guide')) => __('Help').__(' :').' '.__('Guidebook'),
    url_for(getMetaArticleRoute('help_forum', false)) => __('Help').__(' :').' '.__('Forums'),
    url_for('users/sortPreferedLanguages') => __('Set languages preferences')
);
if ($is_connected)
{
    $menu_more[url_for('users/mypage')] = __('personal page');
    $menu_more[url_for('/forums/message_list.php')] =__('mailbox');
}
?>
<div id="mobile_menu">
  <div id="menu_items">
    <div class="menu_entry">
      <div class="menu_item left">
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
      <div class="menu_item middle">
        <?php echo f_link_to('<span class="select_button">' . __('Forums') . '</span>',
                           '?lang='.$lang); ?>
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
<?php echo javascript_queue('$(".menu_select").change(function() { window.location = $(this).find("option:selected").val(); });') ?>
