<?php 
use_helper('Pagination', 'Field', 'SmartDate', 'SmartFormat', 'sfBBCode', 'Viewer', 'ModalBox', 'Lightbox', 'Javascript', 'MyImage');
$mobile_version =  c2cTools::mobileVersion();

echo display_title(__('recent conditions'), 'outings', false);

if (!$mobile_version)
{
    echo '<div id="nav_space">&nbsp;</div>';
    include_partial('nav4list');
}
$conditions_statuses = sfConfig::get('mod_outings_conditions_statuses_list');

echo display_content_top('list_content');
echo start_content_tag('outings_content');

if (!isset($items) || count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __('outings')));
else:
    echo '<p class="list_header">' . link_to_outings(__('Show as a list')) . '</p>';
    
    $pager_navigation = pager_navigation($pager, array('list_header'));
    echo $pager_navigation;
    
    $class = 'recent_conditions';
    if ($show_images)
    {
        $class .= ' condimg';
        echo javascript_tag('lightbox_msgs = Array("' . __('View image details') . '","' . __('View original image') . '");');
    }
?>
<ul class="<?php echo $class ?>">
    <?php
    foreach ($items as $item): ?>
    <li><?php
        $i18n = $item['OutingI18n'][0];
        echo '<span class="item_title">' .
             format_date($item['date'], 'dd/MM/yyyy') . ' - ' .
             get_paginated_activities($item['activities']) . ' - ' .
             link_to($i18n['name'],
                     '@document_by_id_lang_slug?module=outings&id=' . $i18n['id'] . '&lang=' . $i18n['culture'] . '&slug=' . make_slug($i18n['name'])) . ' - ' .
             displayWithSuffix($item['max_elevation'], 'meters') . ' - ' .
             field_route_ratings_data($item, false, true);
        if (isset($item['nb_images']))
        {
            echo ' - ' . picto_tag('picto_images', __('nb_linked_images')) . '&nbsp;' . $item['nb_images'];
        }
        if (isset($item['nb_comments']))
        {
            echo ' - ' . picto_tag('action_comment', __('nb_comments')) . '&nbsp;' . link_to($item['nb_comments'], '@document_comment?module=outings&id='
        . $item['OutingI18n'][0]['id'] . '&lang=' . $item['OutingI18n'][0]['culture']);
        }
        echo '</span>';
        ?>
        <ul>
            <?php
            $geoassociations = $item['geoassociations'];
            if (check_not_empty($geoassociations)): ?>
            <li>
            <?php 
            $areas = array();
            foreach ($geoassociations as $geo)
            {
                $areas[] = $geo['AreaI18n'][0]['name'];
            }
            echo implode(', ', $areas);
            ?></li>
            <?php
            endif;
            ?>
            <li><?php echo _format_data('author', link_to($item['creator'], '@document_by_id?module=users&id=' . $item['creator_id'])); ?></li>
            <?php
            // FIXME sfOutputEscaperObjectDecorator shouldn't be used..
            $access_elevation = check_not_empty($item['access_elevation']) && !($item['access_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['access_elevation'] : 0;
            $up_snow_elevation = check_not_empty($item['up_snow_elevation']) && !($item['up_snow_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['up_snow_elevation'] : 0;
            $down_snow_elevation = check_not_empty($item['down_snow_elevation']) && !($item['down_snow_elevation'] instanceof sfOutputEscaperObjectDecorator) ? $item['down_snow_elevation'] : 0;
            if (check_not_empty($access_elevation) || check_not_empty($up_snow_elevation) || check_not_empty($down_snow_elevation)):
            ?>
            <li><?php
                if (check_not_empty($access_elevation))
                {
                    echo field_data_arg_if_set('access_elevation', $access_elevation, '', 'meters') . ' &nbsp; ';
                }
                echo field_data_arg_range_if_set('up_snow_elevation', 'down_snow_elevation', $up_snow_elevation, $down_snow_elevation, 'elevation separator', '', '', 'meters'); ?>
            </li>
            <?php
            endif;
            
            
            $conditions = $item['OutingI18n'][0]['conditions'];
            $conditions_status = $item['conditions_status'];
            $has_conditions_status = is_integer($conditions_status) && array_key_exists($conditions_status, $conditions_statuses);
            $has_conditions = check_not_empty($conditions) && !($conditions instanceof sfOutputEscaperObjectDecorator);
            if ($has_conditions || $has_conditions_status): ?>
                <li><div class="section_subtitle" id="_conditions"><?php echo __('conditions_status') ?></div>
                <?php
                if ($has_conditions_status)
                {
                    echo __($conditions_statuses[$conditions_status]);
                }
                if ($has_conditions)
                {
                    echo parse_links(parse_bbcode($conditions, null, false, false)); // rq: no image in condition pages
                }
                ?></li>
            <?php endif;

            $weather = $item['OutingI18n'][0]['weather'];
            if (check_not_empty($weather) && !($weather instanceof sfOutputEscaperObjectDecorator)): //FIXME sfOutputEscaperObjectDecorator ?>
                <li><div class="section_subtitle" id="_weather"><?php echo __('weather') ?></div><?php echo parse_links(parse_bbcode($weather, null, false, false)) ?></li>
            <?php endif;
            $timing = $item['OutingI18n'][0]['timing'];
            if (check_not_empty($timing) && !($timing instanceof sfOutputEscaperObjectDecorator)): //FIXME sfOutputEscaperObjectDecorator ?>
                <li><div class="section_subtitle" id="_weather"><?php echo __('timing') ?></div><?php echo parse_links(parse_bbcode($timing, null, false, false)) ?></li>
            <?php endif; ?>
        </ul>
    <?php
    $conditions_levels = unserialize($item['OutingI18n'][0]->get('conditions_levels', ESC_RAW));
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        echo conditions_levels_data($conditions_levels);
    }
    
    if ($show_images && isset($item['linked_docs']))
    {
        include_partial('images/linked_images', array('images' => $item['linked_docs'],
                                                   'user_can_dissociate' => false));
    }
    ?>
    </li>
    <?php
    endforeach ?>
</ul>
<?php
    echo $pager_navigation;
endif;

if ($show_images && $nb_images > 0 && !$mobile_version)
{
// FIXME: find and delete sortable_feedback div + don't use javascript for non-ie browsers
echo javascript_tag("
Event.observe(window, 'load', function(){
$$('.image_list .image').each(function(obj){
obj.observe('mouseover', function(e){obj.down('.image_actions').show();obj.down('.image_license').show();});
obj.observe('mouseout', function(e){obj.down('.image_actions').hide();obj.down('.image_license').hide();});
});});");
// FIXME: do a separate JS file for that, and dynamically include it in the response.
}

echo end_content_tag();

include_partial('common/content_bottom') ?>
