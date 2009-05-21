<?php use_helper('SmartDate', 'Forum');

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="last_msgs" class="latest">
<?php include_partial('documents/home_section_title',
                      array('module'            => 'msgs',
                            'custom_title_icon' => 'forum',
                            'custom_title'      => f_link_to(__('Latest threads'), '?lang='. $culture),
                            'custom_rss'        => f_link_to(' ',
                                                             'extern.php?type=rss&amp;action=active',
                                                              array('class' => 'home_title_right picto_rss',
                                                                    'title' => __('Subscribe to latest threads'))))); ?>
<div id="last_msgs_section_container" class="home_container_text">
<?php if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="listed_changes">
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

            $num_replies = $item['num_replies'];
            echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new');
            if ($num_replies > 0): ?>
                <span class="meta"><?php echo f_link_to("($num_replies)", 'viewtopic.php?id=' . $item['id'] . '&action=last') ?></span>
            <?php endif; ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>
<div class="home_link_list">
<?php
$connected = $sf_user->isConnected();
echo f_link_to(__('Forum'), '?lang='. $sf_user->getCulture()) . ' - ';
if ($connected)
{
    echo f_link_to(__('New posts'), 'search.php?action=show_new&lang='.$culture);
}
else
{
   echo f_link_to(__('Recent posts'), 'search.php?action=show_24h&lang='.$culture);
}
?>
</div>
</div>
<?php
echo javascript_tag("setHomeFolderStatus('last_msgs', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
?>
</div>
