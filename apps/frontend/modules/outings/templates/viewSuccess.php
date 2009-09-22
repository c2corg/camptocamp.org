<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Field');

$id = $sf_params->get('id');
$date = field_raw_date_data($document, 'date');
display_page_header('outings', $document, $id, $metadata, $current_version, $date, ', ');

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    // if the user is not a moderator, use javascript to distinguish
    // between document authors and others
    $moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
    if (!$moderator)
    {
        $associated_users_ids = array();
        foreach ($associated_users as $user)
        {
            $associated_users_ids[] = $user['id'];
        }
        echo javascript_tag('var user_is_author = (['.implode(',', $associated_users_ids).'].indexOf(parseInt($(\'name_to_use\').href.split(\'/\')[4])) != -1);');
    }

    echo '<div class="all_associations">';
    include_partial('documents/association_plus', array('associated_docs' => $associated_users, 
                                                    'module' => 'users', 
                                                    'document' => $document,
                                                    'type' => 'uo', // user-outing
                                                    'strict' => true));

    if (count($associated_routes))
    {
        include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                        'module' => 'routes',  // this is the module of the documents displayed by this partial
                                                        'document' => $document,
                                                        'type' => 'ro', // route-outing
                                                        'strict' => true, // strict looking for main_id in column main of Association table
                                                        'display_info' => true));
    }
    if (count($associated_sites))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                        'module' => 'sites',  // this is the module of the documents displayed by this partial
                                                        'document' => $document,
                                                        'type' => 'to', // site-outing
                                                        'strict' => false)); // no strict looking for main_id in column main of Association table
    }

    include_partial('documents/association', array('associated_docs' => $associated_summits, 'module' => 'summits', 'is_extra' => true));
    include_partial('documents/association', array('associated_docs' => $associated_huts, 'module' => 'huts', 'is_extra' => true));
    include_partial('documents/association', array('associated_docs' => $associated_parkings, 'module' => 'parkings', 'is_extra' => true));
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    if (!count($associated_routes))
    {
        include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                        'module' => 'routes',  // this is the module of the documents displayed by this partial
                                                        'document' => $document,
                                                        'type' => 'ro', // route-outing
                                                        'strict' => true, // strict looking for main_id in column main of Association table
                                                        'display_info' => true));
    }
    if (!count($associated_sites))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                        'module' => 'sites',  // this is the module of the documents displayed by this partial
                                                        'document' => $document,
                                                        'type' => 'to', // site-outing
                                                        'strict' => false)); // no strict looking for main_id in column main of Association table
    }
    echo '</div>';
    if (!$moderator)
    {
        echo javascript_tag("if (!user_is_author) { $$('.add_assoc', '.one_kind_association.empty_content').invoke('hide'); }");
    }
}
echo end_section_tag();


include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('summits', 'outings')));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'), 'needs_translation' => $needs_translation, 'images' => $associated_images, 'associated_areas' => $associated_areas));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'author_specific' => !$moderator));
}

include_partial('documents/license', array('license' => 'by-nc-nd'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
