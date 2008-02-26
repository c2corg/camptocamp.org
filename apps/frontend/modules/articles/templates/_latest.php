<div class="latest">
<?php
use_helper('SmartDate');
include_partial('documents/latest_title', array('module' => 'articles'));

if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
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

            $id = $item['id'];
            $lang = $item['culture'];
            
            echo link_to($item['name'], "@document_by_id_lang?module=articles&id=$id&lang=$lang");
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<?php echo link_to(__('articles list'), '@default_index?module=articles', array('class' => 'home_link_list')) ?>
</div>
