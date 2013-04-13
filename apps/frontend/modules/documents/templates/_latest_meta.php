<div id="on_the_web" class="latest">
<?php
use_helper('SmartDate');

if (!isset($default_open))
{
    $default_open = true;
}
$tr_module =  __('meta outings');
include_partial('documents/home_section_title',
                array('module'            => 'on_the_web',
                      'custom_section_id' => 'on_the_web',
                      'custom_title'      => link_to(__('Latest outings from MetaEngine'),
                                                     sfConfig::get('app_meta_engine_base_url')),
                      'custom_rss'        => link_to('',
                                                     sfConfig::get('app_meta_engine_base_url') . 'outings',
                                                     array('class' => 'home_title_right picto_rss',
                                                           'title' => __('Subscribe to latest outings from MetaEngine'))),
                      'custom_title_icon' => 'outings'));
?>
<div id="on_the_web_section_container" class="home_container_text">
<?php
if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="dated_changes">
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
            $lang = substr($item->getDescription(), -2);
            echo link_to($item->getTitle(), $item->getLink(), ($lang != $culture) ? array('hreflang' => $lang) : null)
                 . ' <span class="meta">(' . $item->getAuthorName() . ')</span>';
            ?>
        </li>
    <?php endforeach ?>
    </ul>
<?php endif;?>
<div class="home_link_list">
<?php echo link_to('meta.camptocamp.org', sfConfig::get('app_meta_engine_base_url')) ?>
</div>
</div>
<?php
$cookie_position = array_search('on_the_web', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setHomeFolderStatus(\'on_the_web\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
