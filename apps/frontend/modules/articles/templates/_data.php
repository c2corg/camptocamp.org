<?php use_helper('Field');
// put here meta tags for microdata that cannot be inside ul tags
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=articles&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul id="article_gauche_5050" class="data">
    <?php
    disp_doc_type('article');
    li(field_data_from_list($document, 'categories', 'mod_articles_categories_list', array('multiple' => true, 'microdata' => 'articleSection')));
    li(field_activities_data_if_set($document));
    li(field_data_from_list($document, 'article_type', 'mod_articles_article_types_list'));
    ?>
</ul>
