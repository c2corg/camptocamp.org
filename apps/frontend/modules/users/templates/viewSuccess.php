<?php
use_helper('Language', 'Sections', 'Viewer', 'Field');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);

display_page_header('users', $document, $id, $metadata, $current_version);

echo start_section_tag('Personal information', 'data');
include_partial('data', array('document' => $document, 'forum_nickname' => $forum_nickname));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    echo '</div>';

    // if the user is not a moderator, use javascript to distinguish
    // between document owner and others
    if ($is_connected && !$is_moderator && $is_not_merged)
    {
        echo javascript_tag('var user_is_author = ('.$id.' == parseInt($(\'name_to_use\').href.split(\'/\').reverse()[0]))');
    }
}
echo end_section_tag();

echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'images' => $associated_images, 'filter_image_type' => false));
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('users')));

if ($is_not_archive && $is_not_merged)
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
                                  : (" - $height_diff_up" . __('meters'));
                echo link_to($doc['name'],
                             "@document_by_id_lang_slug?module=outings&id=" . $doc['id'] . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']))
                         . ' - ' . field_activities_data($doc, true, false)
                         . ' - ' . $doc['date'] . $height_diff_up;
            ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php
    else:
        echo __('This user does not have any associated outing.');
    endif;
    include_partial('outings/linked_outings', array('id' => $id, 'module' => 'users', 'nb_outings' => $nb_associated_outings));
    echo end_section_tag();
    
    echo start_section_tag('User contributions', 'contributions');
    ?>
    <ul class="contribs">
        <li><span class="picto action_comment"></span> <?php echo f_link_to(__('User-s messages'), 'search.php?action=search&author=' . urlencode($forum_nickname)) ?></li>
        <li><span class="picto picto_images"></span> <?php echo link_to(__('Images uploaded by this user'), "images/list?users=$id") ?></li>
        <li><span class="picto picto_articles"></span> <?php echo link_to(__('Personal articles'), "articles/list?users=$id") ?></li>
        <li><span class="picto action_description"></span> <?php echo __('Guidebook contribs:') ?>
    <?php
    if (count($contribs) > 0)
    {
        include_partial('documents/list_changes', array('items' => $contribs,
                                                        'needs_username' => false)); 
        echo '<p><span class="picto action_list"></span> ' . link_to(__('List all user contribs'), "documents/whatsnew?user=$id") . '</p>';
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
                                              'dissociation' => 'moderator',
                                              'author_specific' => !$moderator,
                                              'is_protected' => $document->get('is_protected')));

}

include_partial('documents/license', array('license' => 'by-nc-nd'));

echo end_content_tag();

include_partial('common/content_bottom');

