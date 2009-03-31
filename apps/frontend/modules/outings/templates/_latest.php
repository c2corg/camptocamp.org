<div id="last_outings" class="latest">
<?php
use_helper('SmartDate', 'Pagination');

if (!isset($open))
{
    $open = true;
}
include_partial('documents/home_section_title',
                array('module'            => 'outings',
                      'open'              => $open,
                      'custom_title_link' => '@ordered_list?module=outings&orderby=date&order=desc')); ?>
<div id="last_outings_section_container" class="home_container_text" <?php if (!$open) echo 'style="display: none;"'; ?>>
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="dated_changes">
    <?php 
    $date = $list_item = 0;
    foreach ($items as $item): ?>
        <?php
            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $timedate = $item['date'];
            if ($timedate != $date)
            {
                echo '<span class="date">' . format_date($timedate, 'dd/MM') . '</span>';
                $date = $timedate;
            }
            echo get_paginated_activities($item['activities']) . ' '; 

            $i18n = $item['OutingI18n'][0];
            $id = $item['id'];
            $lang = $i18n['culture'];
            
            echo link_to($i18n['name'], "@document_by_id_lang_slug?module=outings&id=$id&lang=$lang&slug=" . formate_slug($i18n['search_name']));
            
            $outing_data = array();
            
            /*
            $max_elevation = displayWithSuffix($item['max_elevation'], 'meters');
            if (!empty($max_elevation))
            {
                $outing_data[] = $max_elevation;
            }
            */

            $area_name = Area::getBestRegionDescription($item['geoassociations']);
            if (!empty($area_name))
            {
                $outing_data[] = $area_name;
            }

            if (count($outing_data) > 0)
            {
                echo ' <span class="meta">(' .  implode(' - ', $outing_data) . ')</span>';
            }
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<div class="home_link_list">
<?php echo link_to(__('outings list'), '@ordered_list?module=outings&orderby=date&order=desc')
           . ' - ' .
           link_to(__('recent conditions'), 'outings/conditions')
           . ' - ' .
           link_to(__('Prepare outing'), getMetaArticleRoute('prepare_outings'))
           . ' - ' .
           button_wizard(); ?>
</div>
</div>
</div>
