<?php 
use_helper('Pagination', 'Field', 'SmartDate', 'SmartFormat', 'sfBBCode', 'Viewer');

echo display_title(__('recent conditions'), 'outings');

echo '<div id="nav_space">&nbsp;</div>';
include_partial('nav4list');
$conditions_statuses = sfConfig::get('mod_outings_conditions_statuses_list');
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="outings_content">
<?php 
$items = $pager->getResults('array', ESC_RAW);
// FIXME the following shouldn't be in the template, but easier so
$items = Language::getTheBestForOutingsAssociatedAreas($items);

if (count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __('outings')));
else:
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
?>
<ul class="recent_conditions">
    <?php foreach ($items as $item): ?>
    <li><?php
        $i18n = $item['OutingI18n'][0];
        echo format_date($item['date'], 'dd/MM') . ' - ' .
             get_paginated_activities($item['activities']) . ' - ' .
             link_to($i18n['name'],
                     '@document_by_id_lang_slug?module=outings&id=' . $i18n['id'] . '&lang=' . $i18n['culture'] . '&slug=' . formate_slug($i18n['search_name'])) . ' - ' .
             displayWithSuffix($item['max_elevation'], 'meters');
        ?></td>
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

            $access_elevation = $item['access_elevation'];
            $up_snow_elevation = $item['up_snow_elevation'];
            $down_snow_elevation = $item['down_snow_elevation'];
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
            $has_conditions_status = check_not_empty($conditions_status) && array_key_exists($conditions_status, $conditions_statuses);
            $has_conditions = check_not_empty($conditions);
            if ($has_conditions || $has_conditions_status): ?>
                <li><em><?php echo __('conditions_status') ?></em>
                <?php
                if ($has_conditions_status)
                {
                    echo __($conditions_statuses[$conditions_status]);
                }
                if ($has_conditions)
                {
                    echo parse_links(parse_bbcode($conditions));
                }
                ?></li>
            <?php endif;

            $weather = $item['OutingI18n'][0]['weather'];
            if (check_not_empty($weather)): ?>
                <li><em><?php echo __('weather') ?></em><?php echo parse_links(parse_bbcode($weather)) ?></li>
            <?php endif; ?>
    </ul>
    <?php
    $conditions_levels = unserialize($item['OutingI18n'][0]['conditions_levels']);
    if (!empty($conditions_levels) && count($conditions_levels))
    {
        echo conditions_levels_data($conditions_levels);
    }
    ?>
    </li>
    <?php endforeach ?>
</ul>
<?php
    echo $pager_navigation;
endif; ?>
</div>
</div>

<?php include_partial('common/content_bottom') ?>
