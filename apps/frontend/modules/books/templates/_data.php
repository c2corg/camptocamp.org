<?php 
use_helper('Field');
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=books&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul id="article_gauche_5050" class="data">
    <?php
    disp_doc_type('book');
    li(field_data_if_set($document, 'author', array('microdata' => 'author')));
    li(field_data_if_set($document, 'editor', array('microdata' => 'publisher')));
    li(field_data_if_set($document, 'isbn', in_array('18', $document->getRaw('book_types')) ?
        array('title' => 'issn') : array('title' => 'isbn', 'microdata' => 'isbn')));
    li(field_url_data_if_set($document, 'url', array('microdata' => 'url')));
    li(field_activities_data($document));
    li(field_data_if_set($document, 'nb_pages', array('microdata' => 'numberOfPages')));
    li(field_data_if_set($document, 'publication_date', array('microdata' => 'datePublished')));
    li(field_data_from_list_if_set($document, 'langs', 'app_languages_book',
        array('multiple' => true, 'microdata' => 'inLanguage')));
    li(field_data_from_list_if_set($document, 'book_types', 'mod_books_book_types_list', array('multiple' => true)));
    ?>
</ul>
