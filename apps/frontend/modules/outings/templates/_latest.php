<div class="latest">
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
            $timedate = $item['date'];
            if ($timedate != $date)
            {
                // FIXME: what if displaying dates in english?
                echo '<span>' . format_date($timedate, 'dd/MM') . '</span>';
                $date = $timedate;
            }

            $id = $item['id'];
            $lang = $item['culture'];
            
            echo link_to($item['name'], "@document_by_id_lang?module=outings&id=$id&lang=$lang");
            echo ' ' . get_paginated_activities($item['activities']); 
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<?php echo link_to(__('outings list'), '@default_index?module=outings', array('class' => 'home_link_list')) ?>
</div>
