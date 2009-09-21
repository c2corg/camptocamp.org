<?php
use_helper('Language', 'Sections', 'Viewer'); 

$id = $sf_params->get('id');
$needs_add_display = ($sf_user->isConnected() && (!$document->get('is_protected') || $sf_user->hasCredential('moderator')));

display_page_header('huts', $document, $id, $metadata, $current_version, '', '', $section_list);

// lang-independent content starts here

echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association_plus', array('associated_docs' => $associated_parkings,
                                                   'module' => 'parkings',
                                                   'document' => $document,
                                                   'type' => 'ph', // parking-hut
                                                   'strict' => true));

    include_partial('documents/association', array('associated_docs' => $associated_sites, 'module' => 'sites')); 
    // NB : associations can be deleted on sites pages
    
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles')); 
    // NB : associations can be deleted on articles pages
    
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
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

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $id, 'module' => 'hut'));
    echo end_section_tag();

    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'type' => 'hr', // route-hut, reversed
                                                  'strict' => true));
    echo end_section_tag();
    
    if ($section_list['books'] || $needs_add_display)
    {
        echo start_section_tag('Linked books', 'linked_books');
        include_partial('books/linked_books', array('associated_books' => $associated_books,
                                                    'document' => $document,
                                                    'type' => 'bh', // hut-book, reversed
                                                    'strict' => true,
                                                    'needs_add_display' => $needs_add_display));
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
