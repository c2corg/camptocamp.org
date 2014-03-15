<?php 
use_helper('Pagination', 'Viewer', 'FilterForm', 'MyForm');

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

endif;

if ($nb_results == 0):
    echo '<p class="list_header">' . __('there is no %1% to show', array('%1%' => __($module))) . '</p>';
else:
    $pager_navigation = pager_navigation($pager);
    
    if ($layout != 'light')
    {
        if (!$mobile_version)
        {
            echo '<p class="list_header">' . __('to sort by one column, click once or twice in its title') . '</p>';
        }
        echo '<p class="list_header">' . link_to_default_order(__('sort by id'), __('the list is sorted by id'));
        if ($module == 'outings')
        {
            $orderby_images = array('orderby' => 'odate', 'order' => 'desc');
        }
        else
        {
            $orderby_images = array();
        }
        echo ' &nbsp; ' . link_to_associated_images(__('List all linked images'), $module, $orderby_images);
        echo '</p>';
    }
    
    $orderby_params = array('orderby', 'orderby2', 'orderby3');
    $order_params = array('order', 'order2', 'order3');
    $orderby_list = c2cTools::getRequestParameterArray($orderby_params, '');
    $order_list = c2cTools::getRequestParameterArray($order_params, '');
    
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
                        . ' ' . c2c_submit_tag(__('Send'), array('picto' => 'action_list', 'name' => 'commit', 'class' => 'samesize'))
                        . '</div>';

        $result_types_filter_2 = '<div class="list_form">'
                        . __('Show') . ' ' . $result_type_select_2
                        . ' &nbsp; ' . $linked_doc_select_2
                        . ' ' . c2c_submit_tag(__('Send'), array('picto' => 'action_list', 'name' => 'commit_2', 'class' => 'samesize'))
                        . '</div>';
        
        $params = packUrlParameters('', array('orderby', 'orderby2', 'orderby3', 'order', 'order2', 'order3', 'page'));
        
        echo '<form id="filterform" action="/' . $module . '/listredirect" method="post"><div>
        <input type="hidden" value="' . $params . '" name="params" />';
        foreach ($orderby_params as $key => $orderby_params)
        {
            echo '<input type="hidden" value="' . $orderby_list[$key] . '" name="' . $orderby_params . '" />'
               . '<input type="hidden" value="' . $order_list[$key] . '" name="' . $order_params[$key] . '" />';
        }
        echo '</div>';
        echo $pager_navigation;
        echo $result_types_filter;
        echo pager_nb_results($pager);
    }
    else
    {
        echo $pager_navigation;
        echo pager_nb_results($pager);
    }

if (!$mobile_version): ?>
<table class="list">
    <thead>
        <tr><?php
        include_partial($module . '/list_header', array('custom_fields' => $sf_data->getRaw('custom_fields'),
                                                        'activities'    => $sf_data->getRaw('activities')));
        ?></tr>
    </thead>
    <tbody>
        <?php
        $date = 0;
        $orderby_date = in_array('date', $orderby_list);
        foreach ($items as $item)
        {
            $table_class = ($table_list_even_odd++ % 2 == 0) ? 'table_list_even' : 'table_list_odd';
            $date_light = false;
            if ($orderby_date)
            {
                $timedate = $item['date'];
                if ($timedate != $date)
                {
                    $date = $timedate;
                }
                else
                {
                    $date_light = true;
                }
            }
            
            echo '<tr class="' . $table_class . '">';
            include_partial($module . '/list_body', array('item' => $item, 'custom_fields' => $sf_data->getRaw('custom_fields'), 'table_class' => $table_class, 'date_light' => $date_light));
            echo '</tr>';
        } ?>
    </tbody>
</table>
<?php else: ?>
<ul class="list">
<?php foreach ($items as $item): ?>
    <?php $item_class = ($table_list_even_odd++ % 2 == 0) ? 'list_even' : 'list_odd'; ?>
    <li class="<?php echo $item_class ?>"><?php include_partial($module . '/mobile_list_body', array('item' => $item, 'item_class' => $item_class)); ?></li>
<?php endforeach ?>
</ul>
<?php
    endif; 

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
    if (!$mobile_version)
    {
        echo '<p class="list_footer">' . __($module . ' presentation').'</p>';
    }
    
    echo end_content_tag();
    
    include_partial('common/content_bottom');
}
