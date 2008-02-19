<div class="latest" id="last-outings">
<?php
use_helper('SmartDate', 'Pagination');
include_partial('documents/latest_title', array('module' => 'outings'));

if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
    <?php 
    $date = 0;
    foreach ($items as $item): ?>
        <li><?php 
            echo get_paginated_activities($item['activities']) . ' '; 
            $timedate = $item['date'];
            if ($timedate != $date)
            {
                // FIXME: what if displaying dates in english?
                echo '<span>' . format_date($timedate, 'dd/MM') . '</span>';
                $date = $timedate;
            }

            // FIXME: test prefered language? (most outings are monolingual)
            $id = $item['id'];
            $lang = $item['OutingI18n'][0]['culture'];
            
            echo link_to($item['OutingI18n'][0]['name'], "@document_by_id_lang?module=outings&id=$id&lang=$lang");

            $geo = $item['geoassociations'];
            $nb_geo = count($geo);
            if ($nb_geo == 1)
            {
                // FIXME: prefered language?
                echo ' (' . $geo[0]['AreaI18n'][0]['name'] . ')';
            }
            elseif ($nb_geo > 1)
            {
                $areas = $types = $regions = array();
                
                foreach ($geo as $g)
                {
                    // FIXME: prefered language?
                    if (empty($g['AreaI18n'][0])) continue;

                    $area = $g['AreaI18n'][0];
                    $types[] = !empty($area['Area']['area_type']) ? $area['Area']['area_type'] : 0;
                    $areas[] = $area['name'];
                }

                // use ranges if any
                $rk = array_keys($types, 1);
                if ($rk)
                {
                    foreach ($rk as $r)
                    {
                        $regions[] = $areas[$r];
                    }
                }
                else
                {
                    // else use dept/cantons if any
                    $ak = array_keys($types, 3);
                    if ($ak)
                    {
                        foreach ($ak as $a)
                        {
                            $regions[] = $areas[$a];
                        }
                    }
                    else
                    {
                        // else use what's left (coutries)
                        $regions = $areas;
                    }
                }

                echo ' (' . implode(', ', $regions) . ')';
            }
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<?php echo link_to(__('outings list'), '@default_index?module=outings', array('class' => 'home_link_list2')) . ' - ' .
           link_to(__('recent conditions'), 'outings/conditions', array('class' => 'home_link_list2',
                                                                        'style' => 'margin-left:0')) ?>
</div>
