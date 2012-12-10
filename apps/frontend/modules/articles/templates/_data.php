<?php use_helper('Field'); ?>
<ul id="article_gauche_5050" class="data">
    <?php
    disp_doc_type('article');
    li(field_data_from_list($document, 'categories', 'mod_articles_categories_list', array('multiple' => true)));
    li(field_activities_data_if_set($document));
    li(field_data_from_list($document, 'article_type', 'mod_articles_article_types_list'));
    ?>
</ul>
