<?php
use_helper('General');
?>
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
                            'has_title_link'    => false,
                            'has_title_rss'     => true,
                            'custom_title_text' => __('Latest documents'),
                            'custom_rss'        => link_to('',
                                                           $sf_request->getUriPrefix() . '/static/rss/latest_docs.rss',
                                                           array('class' => 'home_title_right picto_rss',
                                                                 'title' => __("Subscribe to latest documents creations"))))); ?>
<div id="last_docs_section_container" class="home_container_text">
<?php
try
{
    $rssfile = SF_ROOT_DIR . '/web/static/rss/latest_docs.rss';
    if (file_exists($rssfile)) {
        $rss = file_get_contents($rssfile);
        $feed = sfFeedPeer::createFromXml($rss, $sf_request->getUriPrefix() . '/documents/latest');
        $items = array_reverse(sfFeedPeer::aggregate(array($feed))->getItems(), true);
    } else {
        $items = array();	
    }
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
    $max_items = c2cTools::mobileVersion() ? sfConfig::get('app_recent_documents_latest_docs_mobile_limit') : 100;
    foreach ($items as $item): ?>
        <?php
            if ($list_item > $max_items) break;

            // Add class to know if li is odd or even
            if ($list_item%2 == 1): ?>
                <li class="odd">
            <?php else: ?>
                <li class="even">
            <?php endif;
            $list_item++;

            $link = $item->getLink();
            list($unused, $unused , $unused, $doc_module, $doc_id, $doc_lang, $doc_slug) = explode('/', $link);
            $title = $item->getTitle();
            // be sure to have non breakable spaces and ' :' ok with language
            if ($doc_module == 'routes')
            {
                $title = strtr($title, array(' :' => __('&nbsp;:')));
            }
            echo '<div class="picto picto_' . $doc_module . '" title="' . __($doc_module) . '"></div>';
            echo '<div class="last_docs_list_text">';
            echo link_to($title,
                         "@document_by_id_lang_slug?module=$doc_module&id=$doc_id&lang=$doc_lang&slug=$doc_slug",
                         array('hreflang' => $doc_lang));
            echo '</div>';
        ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif; ?>
</div>
<?php
$cookie_position = array_search('last_docs', sfConfig::get('app_personalization_cookie_fold_positions'));
echo javascript_tag('C2C.setSectionStatus(\'last_docs\', '.$cookie_position.', '.((!$default_open) ? 'false' : 'true').");");
?>
</div>
