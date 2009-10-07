<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Field', 'SmartFormat', 'sfBBCode');

$id = $sf_params->get('id');
$date = field_raw_date_data($document, 'date');
display_page_header('outings', $document, $id, $metadata, $current_version, $date, ', ');

// lang-independent content starts here

echo start_section_tag('Information', 'data');

$participants = explode("\n", $document->get('participants'), 1);
if (!empty($participants[0]))
{
    $participants_0 = parse_links(parse_bbcode_simple($participants[0]));
}
else
{
    $participants_0 = '';
}
if (isset($participants[1]))
{
    $participants_1 = _format_text_data('participants', $participants[1], null,
                                           array('needs_translation' => $needs_translation,
                                                 'show_label' => $document->isArchive(),
                                                 'show_images' => false));
}
else
{
    $participants_1 = '';
}
echo '<div class="all_associations col_left col_66">';
if (!$document->isArchive())
{
    if (!empty($participants))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_users, 
                                                            'extra_docs' => array($participants_1),
                                                            'module' => 'users', 
                                                            'document' => $document,
                                                            'inline' => true,
                                                            'merge_inline' => $participants_0,
                                                            'type' => 'uo', // user-outing
                                                            'strict' => true));
    }
    
    include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                    'module' => 'routes',  // this is the module of the documents displayed by this partial
                                                    'document' => $document,
                                                    'type' => 'ro', // route-outing
                                                    'strict' => true, // strict looking for main_id in column main of Association table
                                                    'display_info' => true));
}
elseif (!empty($participants))
{
    include_partial('documents/association', array('associated_docs' => $associated_users, 
                                                   'extra_docs' => array($participants),
                                                   'module' => 'users', 
                                                   'inline' => true));
}
echo '</div>';

if (!$document->isArchive())
{
    // if the user is not a moderator, but connected, use javascript to distinguish
    // between document authors and others
    $moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
    if ($sf_user->isConnected() && !$moderator)
    {
        $associated_users_ids = array();
        foreach ($associated_users as $user)
        {
            $associated_users_ids[] = $user['id'];
        }
        echo javascript_tag('var user_is_author = (['.implode(',', $associated_users_ids).'].indexOf(parseInt($(\'name_to_use\').href.split(\'/\')[4])) != -1);');
    }

    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
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
    echo '</div>';
}

include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    if (!count($associated_sites))
    {
        echo '<div class="all_associations col_left col_66">';
        include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                        'module' => 'sites',  // this is the module of the documents displayed by this partial
                                                        'document' => $document,
                                                        'type' => 'to', // site-outing
                                                        'strict' => false)); // no strict looking for main_id in column main of Association table
        echo '</div>';
    }
    if ($sf_user->isConnected() && !$moderator)
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
