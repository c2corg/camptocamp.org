<?php
use_helper('Language', 'Sections', 'Viewer'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected);

display_page_header('huts', $document, $id, $metadata, $current_version, '', '', $section_list);

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_parkings,
                              'module' => 'parkings',
                              'route_list_module' => 'huts',
                              'route_list_ids' => $id,
                              'route_list_linked' => true,
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ph', // parking-hut
                              'strict' => true));

        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ht', // hut-site
                              'strict' => true ));
    }
    
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true,
                          'avalanche_bulletin' => true));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'hc',
                              'strict' => true));
        
        if ($show_link_tool)
        {
            if ($document->get('shelter_type') == 5)
            {
                $modules_list = array('parkings', 'sites', 'books', 'articles');
            }
            else
            {
                $modules_list = array('parkings', 'routes', 'sites', 'books', 'articles');
            }
            
            echo c2c_form_add_multi_module('huts', $id, $modules_list, 9, 'multi_1', true);
        }
    }
    
    echo '</div>';
}
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('summits', 'huts')));

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('Linked outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $id, 'module' => 'huts'));
    echo end_section_tag();

    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $id,
                                                  'module' => 'huts',
                                                  'type' => 'hr', // route-hut, reversed
                                                  'strict' => true));
    echo end_section_tag();
    
    if ($section_list['books'])
    {
        echo start_section_tag('Linked books', 'linked_books');
        include_partial('books/linked_books', array('associated_books' => $associated_books,
                                                    'document' => $document,
                                                    'type' => 'bh', // hut-book, reversed
                                                    'strict' => true));
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
