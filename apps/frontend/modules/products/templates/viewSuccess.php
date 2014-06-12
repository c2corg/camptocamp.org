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
$section_list = array('map' => (boolean)($document->get('geom_wkt')));
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

// we can have multiple product type values
$product_types = $document->get('product_type');
if ($product_types->count() == 1)
{
    switch ($product_types[0])
    {
        case 2: $item_type = 'Restaurant'; break;
        case 3: $item_type = 'GroceryStore'; break;
        case 4: $item_type = 'BarOrPub'; break;
        case 5: $item_type = 'SportingGoodsStore'; break;
        case 1:
        default: $item_type = 'LocalBusiness'; break;
    }
}
else
{
    $item_type = 'LocalBusiness';
}
display_page_header('products', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/'.$item_type,
                          'nb_comments' => $nb_comments));

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
                              'route_list_module' => 'products',
                              'route_list_ids' => $id,
                              'route_list_linked' => true,
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'pf', // parking-product
                              'strict' => true));
    }
    
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'fc',
                              'strict' => true));
        
        if ($show_link_tool)
        {
            $modules_list = array('parkings', 'articles');
            
            echo c2c_form_add_multi_module('products', $id, $modules_list, 9, array('field_prefix' => 'multi_1'));
        }
    }
    
    echo '</div>';
    
    include_partial('documents/geom_warning', array('document' => $document));
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    include_partial($mobile_version ? 'documents/mobile_map_section' : 'documents/map_section', array('document' => $document));

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

include_partial('common/content_bottom');
?>
