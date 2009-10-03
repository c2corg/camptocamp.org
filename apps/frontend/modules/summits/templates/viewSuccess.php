<?php 
use_helper('Language', 'Sections', 'Viewer', 'AutoComplete', 'Ajax', 'General');

$id = $sf_params->get('id');
$needs_add_display = ($sf_user->isConnected() && (!$document->get('is_protected') || $sf_user->hasCredential('moderator')));

display_page_header('summits', $document, $id, $metadata, $current_version, '', '', $section_list);

// language-independent content starts here
echo start_section_tag('Information', 'data');

echo '<div class="all_associations col_left col_33">';
include_partial('data', array('document' => $document));

if (!$document->isArchive())
{
    if (count($associated_summits))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_summits, 
                                                            'module' => 'summits', 
                                                            'document' => $document,
                                                            'type' => 'ss', // summit-summit
                                                            'strict' => false )); // no strict looking for main_id in column main of Association table
    }
    echo '</div>';
    
    echo '<div class="all_associations col col_33">';
    include_partial('documents/association', array('associated_docs' => $associated_sites, 'module' => 'sites'));
    include_partial('documents/association', array('associated_docs' => $associated_huts, 'module' => 'huts'));
    include_partial('documents/association', array('associated_docs' => $associated_parkings, 'module' => 'parkings'));
    echo '</div>';
    
    echo '<div class="all_associations col_right col_33">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    
    $extra_maps = $document->get('maps_info');
    $extra_maps = array_map('trim', explode('\\', $extra_maps));
    include_partial('documents/association', array('associated_docs' => $associated_maps,
                                                   'extra_docs' => $extra_maps,
                                                   'module' => 'maps'));
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    
    if (!count($associated_summits))
    {
        include_partial('documents/association_plus', array('associated_docs' => $associated_summits, 
                                                            'module' => 'summits', 
                                                            'document' => $document,
                                                            'type' => 'ss', // summit-summit
                                                            'strict' => false )); // no strict looking for main_id in column main of Association table
    }
    echo '</div>';
}
else
{
    echo '</div>';
}

echo end_section_tag();


// map section starts here
include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array('summits', 'routes')));

// lang-dependent content starts here
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();
// instead of $languages: XSS protection deactivation

// associated routes section starts here
if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked routes', 'routes');
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $summit_ids,
                                                  'module' => 'summits',
                                                  'type' => 'sr', // route - summit, reversed
                                                  'strict' => true));
    
    if ($sf_user->isConnected())
    {
        echo '<div class="add_content">'
             . link_to(picto_tag('picto_add', __('Associate new route')) .
                       __('Associate new route'),
                       "routes/edit?link=$id")
             . '</div>';
    }
    echo end_section_tag();

    echo start_section_tag('Linked outings', 'outings');
    include_partial('outings/linked_outings', array('id' => $summit_ids, 'module' => 'summits'));
    echo end_section_tag();
    
    if ($section_list['books'] || $needs_add_display)
    {
        echo start_section_tag('Linked books', 'linked_books');
        include_partial('books/linked_books', array('associated_books' => $associated_books,
                                                    'document' => $document,
                                                    'type' => 'bs', // summit-book, reversed
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
