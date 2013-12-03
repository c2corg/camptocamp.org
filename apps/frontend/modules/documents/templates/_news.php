<?php use_helper('Forum');

if (count($items) != 0):

$forum_langs = sfConfig::get('app_forum_lang_by_id');

if (!isset($default_open))
{
    $default_open = true;
}
?>
<div id="nav_news" class="nav_box">
    <div class="nav_box_top"></div>
    <div class="nav_box_content">
        <?php echo nav_title('news', __('c2corg news'), 'list'); ?>
        <div class="nav_box_text" id="nav_news_section_container">
            <ul>
            <?php foreach ($items as $item): 
                 $lang = $forum_langs[$item['forum_id']]; ?>
                 <li><?php echo f_link_to($item['subject'], 'viewtopic.php?id=' . $item['id'] . '&action=new',
                                          array('hreflang' => $lang)) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
        <?php
        $cookie_position = array_search('nav_news', sfConfig::get('app_personalization_cookie_fold_positions'));
        echo javascript_tag('C2C.setSectionStatus(\'nav_news\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
        ?>
    </div>
    <div class="nav_box_down"></div>
</div>

<?php endif;
