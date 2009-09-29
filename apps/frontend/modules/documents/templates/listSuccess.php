<?php 
use_helper('Pagination', 'Viewer');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();
$table_list_even_odd = 0;

echo display_title(__($module . ' list'), $module, false);

echo '<div id="nav_space">&nbsp;</div>';
include_partial("$module/nav4list");
//include_partial('documents/nav_news');

echo display_content_top('list_content');
echo start_content_tag($module . '_content');

echo '<p class="list_header">' . __($module . ' presentation').'</p>';
if (!isset($items))
{
    $items = $pager->getResults('array', ESC_RAW);
    if (count($items))
    {
        $items = Language::parseListItems($items, c2cTools::module2model($module));
    }
}

echo '<p class="list_header">';
if (count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __($module))) . '</p>';
else:
    echo __('to sort by one column, click once or twice in its title') . '</p>';
    echo '<p class="list_header">' . link_to_default_order(__('sort by id'), __('the list is sorted by id')) . '</p>';
    
    $pager_navigation = pager_navigation($pager);
    
    if (in_array($module, array('outings', 'routes', 'summits', 'sites', 'parkings', 'huts', 'areas')))
    {
        if ($module == 'outings')
        {
            $outings_filter_tips = 'Show selected outings';
            $conditions_label = 'show conditions of the outings';
        }
        else
        {
            $outings_filter_tips = 'Show outings linked to selected docs';
            $conditions_label = 'show conditions of the linked outings';
        }
        $outings_filter = '<div class="list_form">' . __($outings_filter_tips) . ' <input type="submit" class="picto action_list" value="' . __('Send') . '" name="commit_outings"/></div>';
        
        $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby', '');
        $param_order = sfContext::getInstance()->getRequest()->getParameter('order', '');
        
        echo '<p>' . link_to_conditions(__($conditions_label)) . '</p>';
        echo '<form id="filterform" action="/' . $module . '/listredirect" method="post">
        <input type="hidden" value="' . $param_orderby . '" name="orderby"/>
        <input type="hidden" value="' . $param_order . '" name="order"/>';
        echo $pager_navigation;
        echo $outings_filter;
    }
    else
    {
        echo $pager_navigation;
    }
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
    if (in_array($module, array('outings', 'routes', 'summits', 'sites', 'parkings', 'huts', 'areas')))
    {
        echo $outings_filter;
        echo '</form>';
    }
endif;

echo end_content_tag();

include_partial('common/content_bottom') ?>
