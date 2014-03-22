<?php
use_helper('Language', 'Sections', 'Viewer'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$shelter_type = $document->get('shelter_type');
$is_gite_camping = ($shelter_type == 5 || $shelter_type == 6);
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

// if the document is shelter or bivouac, call it simple Place, else LodgingBusiness
$item_type = in_array($shelter_type, array(2, 3)) ? 'Place' : 'LodgingBusiness';
display_page_header('huts', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/'.$item_type, 'nb_comments' => $nb_comments));

// lang-independent content starts here

echo start_section_tag('Information', 'data');
if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));

if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    if ($is_not_merged)
    {
        if ($is_moderator) // these are ghost summits and shouldn't be displayed to regular users
        {
            include_partial('documents/association',
                            array('associated_docs' => $associated_summits, 
                                  'module' => 'summits', 
                                  'document' => $document,
                                  'show_link_to_delete' => $show_link_to_delete,
                                  'type' => 'sh', // summit-hut
                                  'strict' => true,
                                  'url_options' => 'redirect=no'));
        }
        
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
                              'type' => $is_gite_camping ? '' : 'ht', // hut-site, no link available if gite or camping
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
        if ($show_link_tool)
        {
            if ($is_gite_camping)
            {
                $modules_list = array('parkings', 'sites', 'books', 'articles');
            }
            else
            {
                $modules_list = array('parkings', 'routes', 'sites', 'books', 'articles');
            }
            if ($is_moderator)
            {
                $modules_list[] = 'summits';
            }
            
            echo c2c_form_add_multi_module('huts', $id, $modules_list, 9, 'multi_1', true);
        }
    }
    
    echo '</div>';
    
    include_partial('documents/geom_warning', array('document' => $document));
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images,
                                                'associated_routes' => $associated_summit_routes));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    $document->parkings = $associated_parkings;
}

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($is_not_archive && $is_not_merged)
{
    if (!$is_gite_camping)
    {
        $doc_module = 'huts';
        $type = 'hr'; // route-hut, reversed
    }
    else
    {
        $doc_module = 'parkings';
        $type = ''; // no link to delete
    }

    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $ids,
                                                  'module' => $doc_module,
                                                  'type' => $type,
                                                  'strict' => true));
    echo end_section_tag();
    
    if (!empty($ids))
    {
        echo start_section_tag('Latest outings', 'outings');
        include_partial('outings/linked_outings', array('id' => $ids, 'module' => $doc_module, 'items' => $latest_outings, 'nb_outings' => $nb_outings));
        echo end_section_tag();
    }
    
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

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    // annex docs section
    include_partial('documents/annex_docs',
                    array('document' => $document,
                          'related_articles' => $associated_articles,
                          'related_portals' => $related_portals,
                          'show_link_to_delete' => $show_link_to_delete));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version,
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
?>
