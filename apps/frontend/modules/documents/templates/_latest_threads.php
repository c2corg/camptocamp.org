<?php use_helper('SmartDate', 'Forum'); ?>
<div class="latest">
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo image_tag('/static/images/picto/list.png', 
               array('alt' => __('Forum'), 'title' => __('Forum')))
     . ' ';
echo f_link_to(__('Latest threads'), '?lang='. $sf_user->getCulture());
?>
</span><span class="home_title_right">
<?php
echo f_link_to(image_tag('/static/images/picto/rss.png', array('alt' => __('RSS feed creations'))),
               'extern.php?type=rss&action=active');
?>
</span></div>

<?php if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
    <?php
    $date = 0;
    $list_item = 1;
    foreach ($items as $item): ?>
        <?php 
            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $timedate = format_date($item['last_post'], 'dd/MM');
            if ($date != $timedate)
            {
                echo "<span>$timedate</span>";
                $date = $timedate;
            }
            $num_replies = $item['num_replies'];
            echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new') . 
                 ($num_replies > 0 ? " ($num_replies)" : '');
            ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php 
endif;

echo f_link_to(__('Forum'), '?lang='. $sf_user->getCulture(), array('class' => 'home_link_list'));
?>
</div>
