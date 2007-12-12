<?php 
use_helper('Pagination');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();
$table_list_even_odd = 0;
?>

<div class="clearing">
    <span class="article_title img_title_<?php echo $module ?>"><?php echo __($module . ' list') ?></span>
</div>

<?php
echo '<div id="nav_space">&nbsp;</div>';
include_partial("$module/nav4list");
//include_partial('documents/nav_news');
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article">
<?php 
$items = $pager->getResults('array', ESC_RAW);

if (count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __($module)));
else:
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
    $items = Language::parseListItems($items, c2cTools::module2model($module));
?>
<table class="list">
    <thead>
        <tr><?php include_partial($module . '/list_header'); ?></tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <?php $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd'; ?>
            <tr class="<?php echo $table_class ?>"><?php include_partial($module . '/list_body', array('item' => $item, 'table_class' => $table_class)); ?></tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php
    echo $pager_navigation;
endif; ?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
