<?php 
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete', 'Ajax', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);

switch ($document->get('summit_type'))
{
    case 1: $item_type = 'http://schema.org/Mountain'; break;
    case 5: $item_type = ''; break;
    default: $item_type = 'http://schema.org/Landform'; break;
}
display_page_header('summits', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => $item_type, 'nb_comments' => $nb_comments));

// language-independent content starts here
echo start_section_tag('Information', 'data');

echo '<div class="all_associations col_left col_33">';

if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));

if ($is_not_archive)
{
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_summits, 
                              'module' => 'summits', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ss', // summit-summit
                              'strict' => false )); // no strict looking for main_id in column main of Association table

        echo '</div>';
        
        echo '<div class="all_associations col col_33">';
        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites',  // this is the module of the documents displayed by this partial
                              'reduce_name' => true,
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'st', // summits-site
                              'strict' => true));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_huts,
                              'module' => 'huts',
                              'route_list_module' => 'summits',
                              'route_list_ids' => $ids,
                              'route_list_linked' => true));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_parkings,
                              'module' => 'parkings',
                              'route_list_module' => 'summits',
                              'route_list_ids' => $ids,
                              'route_list_linked' => true));
    }
    echo '</div>';
    
    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true,
                          'avalanche_bulletin' => true,
                          'lat' => $document->get('lat'),
                          'lon' => $document->get('lon')));
    
    if (check_not_empty_doc($document, 'maps_info'))
    {
        $extra_maps = $document->get('maps_info');
        $extra_maps = array_map('trim', explode('\\', $extra_maps));
    }
    else
    {
        $extra_maps = '';
    }
    include_partial('documents/association',
                    array('associated_docs' => $associated_maps,
                          'extra_docs' => $extra_maps,
                          'module' => 'maps'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'sc',
                              'strict' => true));
    }
    echo '</div>';

    if ($show_link_tool)
    {
        echo '<div class="all_associations col_left col_66">';

        $modules_list = array('summits', 'sites', 'books', 'articles');
        $options = array('field_prefix' => 'multi_1');

        if (check_not_empty_doc($document, 'lon'))
        {
            $options['suggest_near_docs'] = array('lon' => $document['lon'], 'lat' => $document['lat']);
            $options['suggest_exclude'] = array('summits' => array_merge(get_directly_linked_ids($associated_summits), array((int)$id)),
                                                'sites' => get_directly_linked_ids($associated_sites));
        }

        echo c2c_form_add_multi_module('summits', $id, $modules_list, 3, $options);

        echo '</div>';
    }
    
    include_partial('documents/geom_warning', array('document' => $document));
}
else
{
    echo '</div>';
}

echo end_section_tag();

// lang-dependent content starts here
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images,
                                                'ids' => $ids));
echo end_section_tag();
// instead of $languages: XSS protection deactivation

// map section starts here
include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section',
                array('document' => $document));

// associated routes section starts here
if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $ids,
                                                  'module' => 'summits',
                                                  'type' => 'sr', // route - summit, reversed
                                                  'strict' => true));
    
    if ($show_link_tool)
    {
        echo '<div class="add_content">'
             . link_to(picto_tag('picto_add', __('Associate new route')) .
                       __('Associate new route'),
                       "routes/edit?link=$id")
             . '</div>';
    }
    echo end_section_tag();

    echo start_section_tag('Latest outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'summits', 'items' => $latest_outings, 'nb_outings' => $nb_outings));
    echo end_section_tag();
    
    if ($section_list['books'])
    {
        echo start_section_tag('Linked books', 'linked_books');
        include_partial('books/linked_books', array('associated_books' => $associated_books,
                                                    'document' => $document,
                                                    'type' => 'bs', // summit-book, reversed
                                                    'strict' => true));
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'list_ids' => $ids,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    include_partial('documents/annex_docs', array('related_portals' => $related_portals));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
