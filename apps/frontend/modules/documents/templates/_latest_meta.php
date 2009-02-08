<div class="latest">
<?php
use_helper('SmartDate');
$tr_module =  __('meta outings');
 ?>
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo '<span class="home_title_outings" title="' . $tr_module . '">'. $tr_module .'</span>';
?>

<a href="<?php echo sfConfig::get('app_meta_engine_base_url') ?>"><?php echo __('Latest outings from MetaEngine') ?></a>
</span><span class="home_title_right">
<?php
echo link_to('',
             sfConfig::get('app_meta_engine_base_url') . 'outings',
             array('class' => 'home_title_rss',
                   'title' => __('Subscribe to latest outings from MetaEngine')));
?>
</span></div>

<?php
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

            $timedate = format_date($item->getPubDate(), 'dd/MM');
            if ($date != $timedate)
            {
                echo "<span class=\"date\">$timedate</span>";
                $date = $timedate;
            }
            echo link_to($item->getTitle(), $item->getLink()) . ' <span class="meta">(' . $item->getAuthorName() . ')</span>';
            ?>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
</div>
