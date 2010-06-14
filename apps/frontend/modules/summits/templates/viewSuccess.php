<?php 
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete', 'Ajax', 'General');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);

display_page_header('summits', $document, $id, $metadata, $current_version, '', '', $section_list);

// language-independent content starts here
echo start_section_tag('Information', 'data');

echo '<div class="all_associations col_left col_33">';

if ($is_not_archive && $is_not_merged)
{
    $document->associated_areas = $associated_areas;
}
include_partial('data', array('document' => $document));

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
                              'type' => 'st', // site-site
                              'strict' => false )); // no strict looking for main_id in column main of Association table
                              // warning : strict is set to false since association can be with other sites
        
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
        echo '</div>';
    }
    
    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association',
                    array('associated_docs' => $associated_areas,
                          'module' => 'areas',
                          'weather' => true,
                          'avalanche_bulletin' => true));
    
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
        
        if ($show_link_tool)
        {
            $modules_list = array('summits', 'sites', 'books', 'articles');
            
            echo c2c_form_add_multi_module('summits', $id, $modules_list, 3, 'multi_1', true);
        }
    }
    echo '</div>';
    
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

    echo start_section_tag('Linked outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $ids, 'module' => 'summits'));
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
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
