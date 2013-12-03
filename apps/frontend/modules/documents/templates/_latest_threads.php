<?php use_helper('SmartDate', 'Forum');

$forum_langs = sfConfig::get('app_forum_lang_by_id');

if (!isset($default_open))
{
    $default_open = true;
}

if (isset($custom_title_text))
{
    $custom_title_text = $sf_data->getRaw('custom_title_text');
}
else
{
    $custom_title_text = __('Latest threads');
}

if (isset($custom_title_link))
{
    $custom_title_link = $sf_data->getRaw('custom_title_link');
}
else
{
    $custom_title_link = '?lang=' . $culture;
}

if (isset($custom_title))
{
    $custom_title = $sf_data->getRaw('custom_title');
}
else
{
    $custom_title = f_link_to($custom_title_text, $custom_title_link);
}

if (isset($custom_rss_link))
{
    $custom_rss_link = $sf_data->getRaw('custom_rss_link');
}
else
{
    $custom_rss_link = 'extern.php?type=rss&action=active';
}

?>
<div id="last_msgs" class="latest">
<?php include_partial('documents/home_section_title',
                      array('module'            => 'msgs',
                            'custom_title_icon' => 'forum',
                            'custom_title'      => $custom_title,
                            'custom_rss'        => f_link_to(' ',
                                                             $custom_rss_link,
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
            $lang = $forum_langs[$item['forum_id']];
            echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new',
                           array('hreflang' => $lang));
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
echo f_link_to(__('Forum'), $custom_title_link) . ' - ';
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
$cookie_position = array_search('last_msgs', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'last_msgs\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
