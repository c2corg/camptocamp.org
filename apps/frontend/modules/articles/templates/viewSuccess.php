<?php
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete', 'General', 'MyForm'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);

display_page_header('articles', $document, $id, $metadata, $current_version);

// lang-dependent content
echo start_section_tag('Article', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'), 'needs_translation' => $needs_translation,
                                                'images' => $associated_images, 'filter_image_type' => ($document->get('article_type') == 1)));
echo end_section_tag();

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if ($is_not_archive)
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    echo '</div>';
}
echo end_section_tag();

if ($is_not_archive && $is_not_merged):

    $is_personal_article = ($document->get('article_type') == 2);
    
    // if the user is not a moderator, and personal article, use javascript to distinguish
    // between document author(s) and others
    $author_specific = !$is_moderator && $is_connected && $is_personal_article;
    if ($author_specific)
    {
        $associated_users_ids = array();
        foreach ($associated_users as $user)
        {
            $associated_users_ids[] = $user['id'];
        }
        echo javascript_tag('var user_is_author = (['.implode(',', $associated_users_ids).'].indexOf(parseInt($(\'name_to_use\').href.split(\'/\').reverse()[0])) != -1)');
    }

    if (!$is_personal_article || count($associated_documents) || $is_connected)
    {
        echo start_section_tag('Linked documents', 'associated_docs');
        include_partial('articles/association', array('document' => $document, 'associated_documents' => $associated_documents));
        echo end_section_tag();
    }

    if (!$is_personal_article || count($associated_images) || $is_connected)
    {
        include_partial('documents/images', array('images' => $associated_images,
                                                  'document_id' => $id,
                                                  'dissociation' => 'moderator',
                                                  'author_specific' => $author_specific,
                                                  'is_protected' => $document->get('is_protected'))); 
    }
    
    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang));

endif;

include_partial('documents/annex_docs', array('related_portals' => $related_portals));

$licenses_array = sfConfig::get('app_licenses_list');
$license = $licenses_array[$document->get('article_type')];
include_partial('documents/license', array('license' => $license, 'large' => $show_link_tool));

if (!$mobile_version)
{
    include_partial('documents/doc_infos', array('version' => $current_version, 'created_at' => $created_at, 'timer' => $timer));
}

echo end_content_tag();

include_partial('common/content_bottom');
