<?php 
use_helper('Pagination', 'Viewer', 'FilterForm');

$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();
$table_list_even_odd = 0;

$mobile_version = c2cTools::mobileVersion();

echo display_title(__($module . ' list'), $module, false, 'list_nav');

if ($layout != 'light'):

if (!$mobile_version)
{
    echo '<div id="nav_space">&nbsp;</div>';
    include_partial("$module/nav4list");
    //include_partial('documents/nav_news');
}

echo display_content_top('list_content');
echo start_content_tag($module . '_content');

echo '<p class="list_header">' . __($module . ' presentation').'</p>';
if (!isset($items) && $nb_results > 0)
{
    $items = $pager->getResults('array', ESC_RAW);
    $items = Language::parseListItems($items, c2cTools::module2model($module));
}

echo '<p class="list_header">';

endif;

if ($nb_results == 0):
    echo __('there is no %1% to show', array('%1%' => __($module))) . '</p>';
else:
    $pager_navigation = pager_navigation($pager);
    
    if ($layout != 'light')
    {
        echo __('to sort by one column, click once or twice in its title') . '</p>';
        echo '<p class="list_header">' . link_to_default_order(__('sort by id'), __('the list is sorted by id')) . '</p>';
    }
    
    if ($layout != 'light' && !$mobile_version &&
        in_array($module, array('outings', 'routes', 'summits', 'sites', 'parkings', 'huts', 'areas', 'users')))
    {
        $result_types = sfConfig::get('app_list_result_types');
        if ($module == 'outings')
        {
            unset($result_types[1]);
            unset($result_types[2]);
        }
        elseif (in_array($module, array('routes', 'sites', 'users')))
        {
            unset($result_types[1]);
        }
        $result_type_select = select_tag('result_type', options_for_select(array_map('__', $result_types), array(3)));
        $result_type_select_2 = select_tag('result_type_2', options_for_select(array_map('__', $result_types), array(3)));
        
        $linked_docs = sfConfig::get('app_list_linked_docs');
        $linked_doc_select = select_tag('linked_docs', options_for_select(array_map('__', $linked_docs), array(1)));
        $linked_doc_select_2 = select_tag('linked_docs_2', options_for_select(array_map('__', $linked_docs), array(1)));
        
        $result_types_filter = '<div class="list_form">'
                        . __('Show') . ' ' . $result_type_select
                        . ' &nbsp; ' . $linked_doc_select
                        . ' <input type="submit" class="picto action_list" value="' . __('Send') . '" name="commit" /></div>';
        
        $result_types_filter_2 = '<div class="list_form">'
                        . __('Show') . ' ' . $result_type_select_2
                        . ' &nbsp; ' . $linked_doc_select_2
                        . ' <input type="submit" class="picto action_list" value="' . __('Send') . '" name="commit_2" /></div>';
        
        $params = packUrlParameters('', array('orderby', 'order', 'page'));
        $param_orderby = sfContext::getInstance()->getRequest()->getParameter('orderby', '');
        $param_order = sfContext::getInstance()->getRequest()->getParameter('order', '');
        
        echo '<form id="filterform" action="/' . $module . '/listredirect" method="post"><div>
        <input type="hidden" value="' . $params . '" name="params" />
        <input type="hidden" value="' . $param_orderby . '" name="orderby" />
        <input type="hidden" value="' . $param_order . '" name="order" /></div>';
        echo $pager_navigation;
        echo $result_types_filter;
        echo pager_nb_results($pager);
    }
    else
    {
        echo $pager_navigation;
        echo pager_nb_results($pager);
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
<?php if ($mobile_version): ?>
<div id="table_slider" class="slider">
    <div class="handle"></div>
</div>
<?php endif ?>
<?php
    echo $pager_navigation;
    if (!$mobile_version &&
        in_array($module, array('outings', 'routes', 'summits', 'sites', 'parkings', 'huts', 'areas', 'users')))
    {
        echo $result_types_filter_2;
        echo '</form>';
    }
endif;

if ($layout != 'light')
{
    echo end_content_tag();
    
    include_partial('common/content_bottom');
}
   
?>
