<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('book');
        li(field_data_if_set($document, 'author'));
        li(field_data_if_set($document, 'editor'));
        li(field_data_if_set($document, 'isbn'));
        li(field_url_data_if_set($document, 'url'));
        li(field_activities_data($document));
        li(field_data_from_list_if_set($document, 'langs', 'app_languages_c2c', true));
        li(field_data_from_list_if_set($document, 'book_types', 'mod_books_book_types_list', true));
        ?>
</ul>   
</div>
