<?php use_helper('SmartDate', 'Forum');

if (count($items) != 0):

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="last_mountain_news" class="latest">
<?php
$conf = sfConfig::get('app_forum_mountain_news_by_lang');
$forums = $conf[$culture];
include_partial('documents/home_section_title',
                array('module'            => 'mountain_news',
                      'custom_title_icon' => 'info',
                      'custom_title'      => f_link_to(__('Latest mountain news'), 'search.php?action=show_news&lang='.$culture),
                      'custom_rss'        => f_link_to(' ',
                                                       'extern.php?type=rss&amp;action=active&fid='.implode(',', $forums),
                                                        array('class' => 'home_title_right picto_rss',
                                                              'title' => __('Subscribe to latest threads')))));
?>
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
$cookie_position = array_search('last_mountain_news', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('setHomeFolderStatus(\'last_mountain_news\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
?>
</div>

<?php endif;
