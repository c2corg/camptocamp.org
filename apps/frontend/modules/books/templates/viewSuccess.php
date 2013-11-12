<?php
use_helper('Language', 'Sections', 'Viewer', 'JavascriptQueue', 'MyMinify'); 

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
$id = $sf_params->get('id');
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_to_delete = ($is_not_archive && $is_not_merged && $is_moderator && !$mobile_version);
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);
$lang = $sf_params->get('lang');
$nb_comments = PunbbComm::GetNbComments($id.'_'.$lang);

display_page_header('books', $document, $id, $metadata, $current_version,
                    array('nav_options' => $section_list, 'item_type' => 'http://schema.org/Book', 'nb_comments' => $nb_comments));

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document, 'nb_comments' => $nb_comments));
if ($is_not_archive)
{
    echo '<div class="all_associations">';
    
    include_partial('areas/association', array('associated_docs' => $associated_areas, 'module' => 'areas'));
    
    if ($is_not_merged)
    {
        include_partial('documents/association',
                        array('associated_docs' => $associated_articles, 
                              'module' => 'articles',
                              'document' => $document,
                              'show_link_to_delete' => $show_link_to_delete,
                              'type' => 'bc',
                              'strict' => true));
    }
    echo '</div>';
}
echo end_section_tag();

// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if ($is_not_archive && $is_not_merged)
{
    // display only sections that are not empty.
    //If every section is empty, display a single 'no attached docs' section

    if ($section_list['routes'])
    {
        echo start_section_tag('Linked routes', 'routes'); // 'routes' instead of 'linked_routes' for fold.js compliance
        include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                      'document' => $document,
                                                      'id' => $id,
                                                      'module' => 'rbooks',
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
        
        $id_no_associated_docs = "no_associated_docs";
        $id_list_associated_docs = "list_associated_docs";
        if ($section_list['docs'])
        {
            echo '<p class="default_text" id="' . $id_no_associated_docs . '">' . __('No associated document found') . '</p>';
        }
        if ($show_link_tool)
        {
            echo '<ul id="' . $id_list_associated_docs . '"><li style="display:none"></li></ul>',
                 '<div id="association_tool" class="plus">',
                 '<p>', __('You can associate this book with existing document using the following tool:'), '</p>';
            
            $modules_list = array('summits', 'sites', 'routes', 'huts', 'articles');
            
            echo c2c_form_add_multi_module('books', $id, $modules_list, 13, $id_list_associated_docs, false, 'indicator', $id_no_associated_docs);
            
            echo '</div>';
        }
        echo end_section_tag();
    }

    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));

    if ($document['isbn'])
    {
        // TODO checks on ISBN value (multiple isbns?)
        $script = minify_get_combined_files_url('/static/js/books.js'); 
        $isbn_or_issn = 'ISBN:';
        foreach ($document['book_types'] as $type)
        {
            if ($type == '18')
            {
                $isbn_or_issn = 'ISSN:';
                break;
            }
        }

        echo start_section_tag('Buy the book', 'buy_books', 'opened', false, false, true);
        echo javascript_queue("$.extend(C2C.GoogleBooks = C2C.GoogleBooks || {}, {
          preview_logo_src: 'http://books.google.com/intl/$lang/googlebooks/images/gbs_preview_button1.png'," . "
          translation: '" . __('Google Book Search') . "'," . "
          book_isbn: '" . $isbn_or_issn . $document['isbn'] . "'});
        $.ajax({
          url: '$script',
          dataType: 'script',
          cache: true })
        .done(function() {
          C2C.GoogleBooks.search();
        });");
        echo end_section_tag();
    }

    if ($mobile_version) include_partial('documents/mobile_comments', array('id' => $id, 'lang' => $lang, 'nb_comments' => $nb_comments));

    include_partial('documents/annex_docs', array('related_portals' => $related_portals));
}

include_partial('documents/license', array('license' => 'by-sa', 'version' => $current_version,
                                           'created_at' => (isset($created_at) ? $created_at :  null),
                                           'timer' => $timer));

echo end_content_tag();

include_partial('common/content_bottom');
?>
