<?php
use_helper('Language', 'Sections', 'Viewer', 'Field', 'Forum', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$section_list = array('map' => (boolean)($document->get('geom_wkt')));

display_page_header('users', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list));

echo start_section_tag('Personal information', 'data');

// if archive, we don't display forum nickname or moderator status
if ($is_not_archive)
{
    include_partial('data', array('document' => $document, 'forum_nickname' => $forum_nickname,
                                  'forum_moderator' => $forum_moderator, 'topoguide_moderator' => $topoguide_moderator));
}
else
{
    include_partial('data', array('document' => $document, 'is_archive' => true));
}

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    echo '</div>';

    // if the user is not a moderator, use javascript to distinguish
    // between document owner and others
    if ($is_connected && !$is_moderator && $is_not_merged)
    {
        echo javascript_tag('if ('.$id.' == document.getElementById("name_to_use").getAttribute("data-user-id")) {
          document.body.setAttribute("data-user-author", true);
        }');
    }
}
echo end_section_tag();

echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                'needs_translation' => $needs_translation, 'images' => $associated_images, 'filter_image_type' => false));
echo end_section_tag();

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('User outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $id, 'module' => 'users', 'items' => $associated_outings, 'nb_outings' => $nb_outings, 'nb_outings_limit' => $nb_outings_limit, 'empty_list_tips' => 'This user does not have any associated outing.'));
    echo end_section_tag();
    
    echo start_section_tag('User contributions', 'contributions');
    ?>
    <ul class="contribs">
        <li><span class="picto action_comment"></span> <?php
        echo f_link_to(__('User-s messages'), 'search.php?action=search&author_id=' .  $id)
           . ' - '
           . f_link_to(__('topics'), 'search.php?action=show_user_topics&user_id=' .  $id)
        ?></li>
        <li><span class="picto picto_images"></span> <?php
            echo link_to(__('Images uploaded by this user'), "images/list?users=$id") . ' - '
               . link_to(__('collaborative images'), "images/list?ityp=1&users=$id") . ' - '
               . link_to(__('personal images'), "images/list?ityp=2&users=$id");
            ?></li>
        <li><span class="picto picto_articles"></span> <?php echo link_to(__('Personal articles'), "articles/list?users=$id") ?></li>
        <li><span class="picto action_description"></span> <?php echo __('Guidebook contribs:');
        echo '<ul>';
        $module_list = array('routes', 'summits', 'sites', 'huts', 'parkings', 'products', 'books', 'areas', 'articles', 'images');
        foreach($module_list as $module)
        {
            $module_title = $module;
            $url_param = '';
            if ($module == 'articles')
            {
                $module_title = 'collaborative articles';
                $url_param = 'ctyp=1&';
            }
            if ($module == 'images')
            {
                $module_title = 'collaborative images';
                $url_param = 'ityp=1&';
            }
            echo '<li>'
               . picto_tag('picto_' . $module) . ' '
               . __($module_title) . __('&nbsp;:') . ' '
               . link_to(__('creations'), "$module/whatsnew?$url_param" . "mode=creations&users=$id") . ' ('
               . link_to(__('tracking'), "$module/whatsnew?$url_param" . "createdby=$id") . ') - '
               . link_to(__('editions'), "$module/whatsnew?$url_param" . "users=$id") . ' ('
               . link_to(__('tracking'), "$module/whatsnew?$url_param" . "editedby=$id") . ')'
               . '</li>';
        }
        echo '</ul>';
        
        if ($is_moderator)
        {
        /*    echo '<p><span class="picto action_list"></span> '
               . link_to(__('List all user collaborative contribs'), "documents/whatsnew?dtyp=collab&users=$id") . ' ('
               . link_to(__('creations'), "documents/whatsnew?dtyp=collab&mode=creations&users=$id") . ')'
               . '</p>';  */
            echo '<p><span class="picto action_list"></span> '
               . link_to(__('List all user contribs'), "documents/whatsnew?users=$id") . ' ('
               . link_to(__('creations'), "documents/whatsnew?mode=creations&users=$id") . ')'
               . '</p>';
        }
    ?>
        </li>
    </ul>
    <?php
    echo end_section_tag();
    
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'author_specific' => !$is_moderator,
                                              'is_protected' => $document->get('is_protected')));


    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang));
}

include_partial('documents/license', array('license' => 'by-nc-nd', 'version' => $current_version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');

