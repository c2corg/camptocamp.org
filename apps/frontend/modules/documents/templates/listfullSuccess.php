<?php 
use_helper('Pagination', 'Field', 'Viewer');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();

echo display_title(__($module . ' list'), $module, false);

echo '<div id="nav_space">&nbsp;</div>';
include_partial("$module/nav4list");

echo display_content_top('list_content');
echo start_content_tag($module . '_content');

if (!isset($items) || count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __('outings')));
else:
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
?>
<ul class="clear">
    <?php foreach ($items as $item): ?>
    <li><?php
        include_partial($module . '/list_full', array('item' => $item));
    ?>
    </li>
    <?php endforeach ?>
</ul>
<?php
    echo $pager_navigation;
endif;

echo end_content_tag();

include_partial('common/content_bottom') ?>
