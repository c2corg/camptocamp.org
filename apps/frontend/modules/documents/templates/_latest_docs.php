<div id="last_docs" class="latest">
<?php
if (!isset($default_open))
{
    $default_open = true;
}
?>
<?php include_partial('documents/home_section_title',
                      array('module'            => 'docs',
                            'custom_title_icon' => 'list',
                            'custom_title_link' => '@whatsnew',
                            'custom_title_text' => __('Latest documents'),
                            'custom_rss'        => link_to('',
                                                           '@creations_feed?module=documents&lang=' . $sf_user->getCulture(),
                                                           array('class' => 'home_title_right picto_rss',
                                                                 'title' => __("Subscribe to latest documents creations"))))); ?>
<div id="last_docs_section_container" class="home_container_text">
<?php
try
{
    $rss = file_get_contents(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'latest_docs.rss');
    $feed = sfFeedPeer::createFromXml($rss, $sf_request->getUriPrefix() . '/documents/latest');
    $items = array_reverse(sfFeedPeer::aggregate(array($feed))->getItems(), true);
}
catch (Exception $e)
{
    $items = array();
}
if (count($items) == 0): ?>
    <p><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="docs_changes">
    <?php
    $list_item = 0;
    $static_base_url = sfConfig::get('app_static_url');
    foreach ($items as $item): ?>
        <?php 
            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $module_name = $item->getDescription();
            echo '<div class="picto picto_' . $module_name . '" alt="' . __($module_name) . '" title="' . __($module_name) . '"></div>';
            echo '<div class="last_docs_list_text">';
            $link = $item->getLink();
            $split = explode('/', $link);
            $lang = $split[5];
            echo link_to($item->getTitle(), $link, ($lang != $culture) ? array('hreflang' => $lang) : null);
            echo '</div>';
        ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>
<div class="home_link_list">
<?php
echo link_to(__('Modifications'), '@whatsnew', array('title' => __('Recent changes'))) . ' ' .
     link_to(image_tag('/static/images/picto/rss.png'),
             '@feed?module=documents&lang=' . $sf_user->getCulture(),
             array('title' => __('Subscribe to latest documents editions'))) . ' - ' .
     link_to(__('Associations'),
             '@latestassociations',
             array('title' => __('Recent associations')));
?>
</div>
</div>
<?php
echo javascript_tag("setHomeFolderStatus('last_docs', ".((!$default_open) ? 'false' : 'true').", '".__('section open')."');");
?>
</div>
