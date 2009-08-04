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
if (!isset($items) || count($items) == 0):
    echo __('there is no %1% to show', array('%1%' => __('outings')));
else:
    $pager_navigation = pager_navigation($pager);
    echo $pager_navigation;
?>
<ul class="recent_conditions">
    <?php foreach ($items as $item): ?>
    <li><?php
        $i18n = $item['OutingI18n'][0];
        echo '<span class="item_title">' .
             format_date($item['date'], 'dd/MM') . ' - ' .
             get_paginated_activities($item['activities']) . ' - ' .
             link_to($i18n['name'],
                     '@document_by_id_lang_slug?module=outings&id=' . $i18n['id'] . '&lang=' . $i18n['culture'] . '&slug=' . formate_slug($i18n['search_name'])) . ' - ' .
             displayWithSuffix($item['max_elevation'], 'meters') . ' - ' .
             field_route_ratings_data($item, false, true);
        if (isset($item['nb_images']))
        {
            echo ' - ' . picto_tag('picto_images', __('nb_images')) . '&nbsp;' . $item['nb_images'];
        }
        if (isset($item['nb_comments']))
        {
            echo ' - ' . picto_tag('action_comment', __('nb_comments')) . '&nbsp;' . link_to($item['nb_comments'], '@document_comment?module=outings&id='
        . $item['OutingI18n'][0]['id'] . '&lang=' . $item['OutingI18n'][0]['culture']);
        }
        echo '</span>';
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
            ?>
            <li><?php
                // get the first one that created the outing (whatever the culture) and grant him as author
                // smaller document version id = older one
                $documents_versions_id = null;
                foreach ($item['versions'] as $version)
                {
                    if (!$documents_versions_id || $version['documents_versions_id'] < $documents_versions_id)
                    {
                        $documents_versions_id = $version['documents_versions_id'];
                        $author_info_name = $version['history_metadata']['user_private_data']['topo_name'];
                        $author_info_id = $version['history_metadata']['user_private_data']['id'];
                    }
                }
                echo _format_data('author', link_to($author_info_name, '@document_by_id?module=users&id=' . $author_info_id));
                ?></li>
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
            $has_conditions = check_not_empty($conditions);
            if ($has_conditions || $has_conditions_status): ?>
                <li><div class="section_subtitle" id="_conditions_status"><?php echo __('conditions_status') ?></div>
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
            if (check_not_empty($weather) && !($weather instanceof sfOutputEscaperObjectDecorator)): //FIXME sfOutputEscaperObjectDecorator ?>
                <li><div class="section_subtitle" id="_weather"><?php echo __('weather') ?></div><?php echo parse_links(parse_bbcode($weather)) ?></li>
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
