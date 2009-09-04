<?php
use_helper('Language', 'Sections', 'Viewer'); 

$id = $sf_params->get('id');
display_page_header('books', $document, $id, $metadata, $current_version, '', '', $section_list);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    // display only sections that are not empty.
    //If every section is empty, display a single 'no attached docs' section

    if ($section_list['routes'])
    {
        echo start_section_tag('Linked routes', 'linked_routes');
        include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                      'document' => $document,
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

    if ($section_list['docs'])
    {
        echo start_section_tag('Linked documents', 'associated_docs');
        echo '<ul id="list_associated_docs">' . __('No associated document found') . '</ul>';
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator'));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
