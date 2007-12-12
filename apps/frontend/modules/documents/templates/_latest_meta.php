<div class="latest">
<?php
use_helper('SmartDate');
$tr_module =  __('meta outings');
 ?>
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo image_tag('/static/images/modules/outings_mini.png',
                array('alt' => $tr_module,
                'title' => $tr_module));
?>

<a href="<?php echo sfConfig::get('app_meta_engine_base_url') ?>"><?php echo __('Latest outings from MetaEngine') ?></a>
</span><span class="home_title_right">
<?php
echo link_to(image_tag('/static/images/picto/rss.png', array('alt' => __('RSS MetaEngine'))),
             sfConfig::get('app_meta_engine_base_url') . 'outings',
             array('title' => __('Subscribe to latest outings from MetaEngine')));
?>
</span></div>

<?php
if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
    <?php 
    $date = 0;
    foreach ($items as $item): ?>
        <li>
            <?php
            $timedate = format_date($item->getPubDate(), 'dd/MM');
            if ($date != $timedate)
            {
                echo "<span>$timedate</span>";
                $date = $timedate;
            }
            echo link_to($item->getTitle(), $item->getLink()) . ' - ' . $item->getAuthorName();
            ?>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
</div>
