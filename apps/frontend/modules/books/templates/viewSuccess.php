<?php
use_helper('Language', 'Sections', 'Viewer'); 

$id = $sf_params->get('id');
display_page_header('books', $document, $id, $metadata, $current_version);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
if (!$document->isArchive())
{
    echo '<div class="all_associations">';
    include_partial('documents/association_plus', array('associated_docs' => $associated_summits, 
                                                    'module' => 'summits', 
                                                    'document' => $document,
                                                    'type' => 'sb', // summit-book 
                                                    'strict' => true ));
    include_partial('routes/association_plus', array('associated_docs' => $associated_routes, 
                                                    'module' => 'routes', 
                                                    'document' => $document,
                                                    'type' => 'rb', // route-book 
                                                    'strict' => true ));
    include_partial('documents/association_plus', array('associated_docs' => $associated_huts, 
                                                    'module' => 'huts', 
                                                    'document' => $document,
                                                    'type' => 'hb', // hut-book 
                                                    'strict' => true ));
    include_partial('documents/association_plus', array('associated_docs' => $associated_sites, 
                                                    'module' => 'sites', 
                                                    'document' => $document,
                                                    'type' => 'tb', // site-book 
                                                    'strict' => true ));
                                                    
    include_partial('documents/association', array('associated_docs' => $associated_articles, 'module' => 'articles'));
    include_partial('documents/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    include_partial('documents/association', array('associated_docs' => $associated_maps, 'module' => 'maps'));
    echo '</div>';
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages')));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'special_rights' => false)); // FIXME: what does that mean, special_rights ?
}

include_partial('documents/license');

echo '</div></div>'; // end <div id="article">

include_partial('common/content_bottom');
?>
