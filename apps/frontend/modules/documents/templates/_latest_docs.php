<div class="latest">
<div class="home_title"><div class="home_title_left"></div><span class="home_title_text">
<?php
echo '<span class="home_title_list" title="' . __('Latest documents') . '">' . __('Latest documents') . '</span>';
?>
</span></div>

<?php
try
{
    $feed = sfFeedPeer::createFromWeb($sf_request->getUriPrefix() . '/documents/latest');
    $items = sfFeedPeer::aggregate(array($feed))->getItems();
}
catch (Exception $e)
{
    $items = array();
}
if (count($items) == 0): ?>
    <p class="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul class="recent-changes">
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
            echo image_tag($static_base_url . '/static/images/modules/' . $module_name . '_mini.png',
                           array('alt' => __($module_name), 'title' => __($module_name)));
            echo ' ';
            echo link_to($item->getTitle(), $item->getLink());
        ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>
</div>
