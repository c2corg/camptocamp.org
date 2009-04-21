<?php use_helper('SmartDate', 'Forum');

if (count($items) != 0):

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="last_mountain_news" class="latest">
<?php include_partial('documents/home_section_title',
                      array('module'            => 'mountain_news',
                            'custom_title_icon' => 'info',
                            'custom_title'      => f_link_to(__('Latest mountain news'), '?lang='. $sf_user->getCulture()), // TODO
                            'custom_rss'        => f_link_to('',
                                                             'extern.php?type=rss&amp;action=active',
                                                              array('class' => 'home_title_right action_rss',
                                                                    'title' => __('Subscribe to latest threads'))))); // TODO ?>
<div id="last_mountain_news_section_container" class="home_container_text">
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
</div>
<?php
echo javascript_tag("setHomeFolderStatus('last_mountain_news', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
?>
</div>

<?php endif;
