<?php
use_helper('Language', 'Sections', 'Viewer', 'Field');
$id = $sf_params->get('id');
display_page_header('users', $document, $id, $metadata, $current_version);

echo start_section_tag('Personal information', 'data');
include_partial('data', array('document' => $document, 'forum_nickname' => $forum_nickname));

if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    echo '</div>';
}

include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('users')));

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag("User outings", 'outings');
    if (count($associated_outings)):
    ?>
        <ul id="list_associated_docs">
        <?php
            foreach ($associated_outings as $doc): ?>
            <li>
            <?php
                $height_diff_up = (string)$doc['height_diff_up'];
                $height_diff_up = empty($height_diff_up) ? ''
                                  : (" - $height_diff_up " . __('meters'));
                echo link_to($doc['name'],
                             "@document_by_id_lang_slug?module=outings&id=" . $doc['id'] . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']))
                         . ' - ' . field_activities_data($doc, true)
                         . ' - ' . $doc['date'] . $height_diff_up;
            ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php
        echo '<p style="margin-top:0.7em;">' .
             image_tag(sfConfig::get('app_static_url') . '/static/images/picto/list.png',
                       array('alt'=> 'List', 'title'=>__('List all user outings'))) . ' ' .
             link_to(__('List all user outings'), "outings/list?user=$id&orderby=date&order=desc") .
             ' - ' .
             image_tag(sfConfig::get('app_static_url') . '/static/images/picto/rss.png',
                       array('alt'=> 'RSS', 'title'=>__('RSS list'))) . ' ' .
             link_to(__('RSS list'), "outings/rss?user=$id&orderby=date&order=desc") .
             '</p>';
    else:
        echo __('This user does not have any associated outing.');
    endif;
    echo end_section_tag();
    
    echo start_section_tag('User contributions', 'contributions');
    ?>
    <ul class="contribs">
        <li><?php echo f_link_to(__('User-s messages'), "search.php?action=show_user&user_id=$id") ?></li>
        <li><?php echo link_to(__('Images uploaded by this user'), "images/list?user=$id") ?></li>
        <li><?php echo __('Guidebook contribs:') ?>
    <?php
    if (count($contribs) > 0)
    {
        include_partial('documents/list_changes', array('items' => $contribs,
                                                        'needs_username' => false)); 
        echo '<p>' . link_to(__('List all user contribs'), "documents/whatsnew?user=$id") . '</p>';
    }
    else
    {
        ?>
        <p><?php echo __('No contribution for this user') ?></p>
        <?php
    }
    ?>
        </li>
    </ul>
    <?php
    echo end_section_tag();
    
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => 'moderator'));

}

include_partial('documents/license');

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
