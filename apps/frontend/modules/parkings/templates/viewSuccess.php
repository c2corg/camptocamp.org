<?php
use_helper('Language', 'Sections', 'Viewer', 'JavascriptQueue');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$lang = $document->getCulture();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

display_page_header('parkings', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/ParkingFacility', 'nb_comments' => $nb_comments));

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
        include_partial('documents/association',
                        array('associated_docs' => $associated_parkings, 
                              'module' => 'parkings', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pp', // parkings-parkings
                              'strict' => false )); // no strict looking for main_id in column main of Association table
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_sites, 
                              'module' => 'sites', 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pt', // parking-site
                              'strict' => true ));
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_huts, 
                              'module' => 'huts',
                              'route_list_module' => 'parkings',
                              'route_list_ids' => $ids,
                              'route_list_linked' => true, 
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'ph', // hut-route
                              'strict' => true )); // strict looking for main_id in column main of Association table
        
        include_partial('documents/association',
                        array('associated_docs' => $associated_products, 
                              'module' => 'products',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pf', // parking-product
                              'strict' => true )); // strict looking for main_id in column main of Association table
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
                              'type' => 'pc',
                              'strict' => true));
        
        if ($show_link_tool)
        {
            $modules_list = array('parkings', 'huts', 'sites', 'routes', 'products', 'articles');
            $options = array('field_prefix' => 'multi_1');
            if (check_not_empty_doc($document, 'lon'))
            {
                $options['suggest_near_docs'] = array('lon' => $document['lon'], 'lat' => $document['lat']);
            }
                   
            echo c2c_form_add_multi_module('parkings', $id, $modules_list, 10, $options);
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
                                                'ids' => $ids));
echo end_section_tag();

include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

if ($is_not_archive && $is_not_merged)
{
    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $ids,
                                                  'module' => 'parkings',
                                                  'type' => 'pr', // route-parking, reversed
                                                  'strict' => true));
    echo end_section_tag();
    
    echo start_section_tag('Latest outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'parkings', 'items' => $latest_outings, 'nb_outings' => $nb_outings));
    echo end_section_tag();

    if ($section_list['books'])
    {
        echo start_section_tag('Linked books', 'linked_books');
        include_partial('books/linked_books', array('associated_books' => $associated_books,
                                                    'document' => $document,
                                                    'type' => 'bp', // do not exist, but $type must have a value
                                                    'strict' => true));
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    include_partial('documents/annex_docs', array('related_portals' => $related_portals));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version, 
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

if (!$mobile_version)
{
  $js = 'if ("geolocation" in navigator) {
navigator.geolocation.getCurrentPosition(function(position) {
$("#get_directions").next().each(function() {
this.href = this.href + \'?lon=\' + position.coords.longitude + \'&lat=\' + position.coords.latitude})})}';
  echo javascript_queue($js);
}

include_partial('common/content_bottom');
?>
