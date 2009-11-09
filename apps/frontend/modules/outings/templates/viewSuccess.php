<?php
use_helper('Language', 'Sections', 'Viewer', 'Ajax', 'AutoComplete', 'Field', 'SmartFormat', 'sfBBCode');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$date = field_raw_date_data($document, 'date');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);

display_page_header('outings', $document, $id, $metadata, $current_version, $date, ', ');

// lang-independent content starts here

echo start_section_tag('Information', 'data');

$participants = explode("\n", $document->get('participants'), 2);
if (!empty(trim($participants[0])))
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
if ($is_not_archive && $is_not_merged)
{
    include_partial('documents/association',
                    array('associated_docs' => $associated_users, 
                          'extra_docs' => array($participants_1),
                          'module' => 'users', 
                          'document' => $document,
                          'inline' => true,
                          'merge_inline' => $participants_0,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'uo', // user-outing
                          'strict' => true));
    
    include_partial('routes/association',
                    array('associated_docs' => $associated_routes, 
                          'module' => 'routes',  // this is the module of the documents displayed by this partial
                          'document' => $document,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'ro', // route-outing
                          'strict' => true, // strict looking for main_id in column main of Association table
                          'display_info' => true));
}
else
{
    echo field_text_data_if_set($document, 'participants', null, array('needs_translation' => $needs_translation, 'show_images' => false));
}
echo '</div>';

if ($is_not_archive)
{
    // if the user is not a moderator, but connected, use javascript to distinguish
    // between document authors and others
    if ($is_connected && !$is_moderator && $is_not_merged)
    {
        $associated_users_ids = array();
        foreach ($associated_users as $user)
        {
            $associated_users_ids[] = $user['id'];
        }
        echo javascript_tag('var user_is_author = (['.implode(',', $associated_users_ids).'].indexOf(parseInt($(\'name_to_use\').href.split(\'/\').reverse()[0])) != -1);');
    }

    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites',  // this is the module of the documents displayed by this partial
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'to', // site-outing
                              'strict' => false)); // no strict looking for main_id in column main of Association table
        
        include_partial('documents/association', array('associated_docs' => $associated_summits, 'module' => 'summits', 'is_extra' => true));
        include_partial('documents/association', array('associated_docs' => $associated_huts, 'module' => 'huts', 'is_extra' => true));
        include_partial('documents/association', array('associated_docs' => $associated_parkings, 'module' => 'parkings', 'is_extra' => true));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'oc',
                              'strict' => true));
    }
    echo '</div>';
}

include_partial('data', array('document' => $document));

if ($show_link_tool)
{
    $modules_list = array('users', 'routes', 'sites', 'articles');
    
    echo '<div class="all_associations empty_content col_left col_66">';
    echo c2c_form_add_multi_module('outings', $id, $modules_list, 2, 'multi_1', true);
    echo '</div>';
}

echo end_section_tag();


include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('summits', 'outings')));

if ($is_not_archive && $is_not_merged && $is_connected && !$is_moderator)
{
    echo javascript_tag("if (!user_is_author) { $$('.add_assoc', '.empty_content', '#map_container p.default_text').invoke('hide'); }");
}

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document,
                                                'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation,
                                                'images' => $associated_images,
                                                'associated_areas' => $associated_areas));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    include_partial('documents/images',
                    array('images' => $associated_images,
                          'document_id' => $id,
                          'dissociation' => 'moderator',
                          'author_specific' => !$is_moderator,
                          'is_protected' => $document->get('is_protected')));
}

include_partial('documents/license', array('license' => 'by-nc-nd'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
