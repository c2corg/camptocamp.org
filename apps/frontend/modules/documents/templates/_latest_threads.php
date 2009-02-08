<?php use_helper('SmartDate', 'Forum'); ?>
<div class="latest">
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo '<span class="home_title_list" title="' . __('Forum') . '">' . __('Forum') . '</span>';
echo f_link_to(__('Latest threads'), '?lang='. $sf_user->getCulture());
?>
</span><span class="home_title_right">
<?php
echo f_link_to('',
               'extern.php?type=rss&action=active',
               array('class' => 'home_title_rss'));
?>
</span></div>

<?php if (count($items) == 0): ?>
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

            $timedate = format_date($item['last_post'], 'dd/MM');
            if ($date != $timedate)
            {
                echo "<span class=\"date\">$timedate</span>";
                $date = $timedate;
            }
            $num_replies = $item['num_replies'];
            echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new');
            if ($num_replies > 0): ?>
                <span class="meta"><?php echo f_link_to("($num_replies)", 'viewtopic.php?id=' . $item['id'] . '&action=last') ?></span>
            <?php endif; ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php 
endif;


$connected = $sf_user->isConnected();
echo f_link_to(__('Forum'), '?lang='. $sf_user->getCulture(), array('class' => 'home_link_list2')) . ' - ';
if ($connected)
{
    echo f_link_to(__('New posts'), 'search.php?action=show_new',
                   array('class' => 'home_link_list2', 'style' => 'margin-left:0'));
}
else
{
   echo f_link_to(__('Recent posts'), 'search.php?action=show_24h',
                   array('class' => 'home_link_list2', 'style' => 'margin-left:0'));
}
?>
</div>
