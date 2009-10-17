<?php
use_helper('Language', 'Sections', 'Viewer'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $document->get('id');
$is_not_archive = (!$document->isArchive() && !$document->get('redirects_to'));
$show_link_to_delete = $is_moderator;
$show_link_tool = ($is_not_archive && $is_connected);

display_page_header('books', $document, $id, $metadata, $current_version, '', '', $section_list);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if ($is_not_archive)
{
    echo '<div class="all_associations">';
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    
    include_partial('documents/association',
                    array('associated_docs' => $associated_articles, 
                          'module' => 'articles',
                          'document' => $document,
                          'show_link_to_delete' => $show_link_to_delete,
                          'type' => 'bc',
                          'strict' => true));
    echo '</div>';
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if ($is_not_archive)
{
    // display only sections that are not empty.
    //If every section is empty, display a single 'no attached docs' section

    if ($section_list['routes'])
    {
        echo start_section_tag('Linked routes', 'linked_routes');
        include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                      'document' => $document,
                                                      'id' => $id,
                                                      'module' => 'books',
                                                      'use_doc_activities' => true,
                                                      'type' => 'br', // route-book, reversed
                                                      'strict' => true,
                                                      'do_not_filter_routes' => true));
        echo end_section_tag();
    }

    if ($section_list['summits'])
    {
        echo start_section_tag('Linked summits', 'linked_summits');
        include_partial('summits/linked_summits', array('associated_summits' => $associated_summits,
                                                        'document' => $document,
                                                        'type' => 'bs', // summit-book, reversed
                                                        'strict' => true));
        echo end_section_tag();
    }

    if ($section_list['huts'])
    {
        echo start_section_tag('Linked huts', 'linked_huts');
        include_partial('huts/linked_huts', array('associated_huts' => $associated_huts,
                                                  'document' => $document,
                                                  'type' => 'bh', // hut-book, reversed
                                                  'strict' => true));
        echo end_section_tag();
    }

    if ($section_list['sites'])
    {
        echo start_section_tag('Linked sites', 'linked_sites');
        include_partial('sites/linked_sites', array('associated_sites' => $associated_sites,
                                                    'document' => $document,
                                                    'type' => 'bt', // site-book
                                                    'strict' => true));
        echo end_section_tag();
    }

    if ($section_list['docs'] || $show_link_tool)
    {
        echo start_section_tag('Linked documents', 'associated_docs');
        if ($section_list['docs'])
        {
            echo '<p id="list_associated_docs" class="default_text">' . __('No associated document found') . '</p>';
        }
        if ($show_link_tool)
        {
            echo '<ul id="list_associated_docs"></ul>'
               . '<div id="plus">'
               . '<p>' . __('You can associate this book with existing document using the following tool:') . '</p>';
            
            $modules_list = array('summits', 'sites', 'routes', 'huts', 'articles');
            
            echo c2c_form_add_multi_module('books', $id, $modules_list, 7, 'list_associated_docs', false);
            
            echo '</div>';
        }
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
