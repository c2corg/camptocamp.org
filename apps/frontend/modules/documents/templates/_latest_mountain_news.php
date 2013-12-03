<?php use_helper('SmartDate', 'Forum');

if (count($items) != 0):

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
    $custom_title_text = __('Latest mountain news');
}

if (isset($custom_title_link))
{
    $custom_title_link = $sf_data->getRaw('custom_title_link');
}
else
{
    $custom_title_link = 'search.php?action=show_news&lang=' . $culture;
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
    $conf = sfConfig::get('app_forum_mountain_news_by_lang');
    $forums = $conf[$culture];
    $custom_rss_link = 'extern.php?type=rss&action=active&fid='.implode(',', $forums);
}

?>
<div id="last_mountain_news" class="latest">
<?php
include_partial('documents/home_section_title',
                array('module'            => 'mountain_news',
                      'custom_title_icon' => 'info',
                      'custom_title'      => $custom_title,
                      'custom_rss'        => f_link_to(' ',
                                                       $custom_rss_link,
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
            $lang = $forum_langs[$item['forum_id']];
            echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new',
                           array('hreflang' => $lang));
            if ($num_replies > 0): ?>
                <span class="meta"><?php echo f_link_to("($num_replies)", 'viewtopic.php?id=' . $item['id'] . '&action=last') ?></span>
            <?php endif; ?>
            </li>
    <?php endforeach ?>
    </ul>
</div>
<?php
$cookie_position = array_search('last_mountain_news', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'last_mountain_news\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>

<?php endif;
