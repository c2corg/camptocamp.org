<div class="latest">
<?php
include_partial('documents/latest_title', array('module' => 'articles'));

if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
    <?php 
    $date = 0;
    foreach ($items as $item): ?>
        <li><?php
            $timedate = $item['day'] . '/' . $item['month']; // FIXME: what about EN format?
            if ($timedate != $date)
            {
                echo "<span>$timedate</span>";
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
