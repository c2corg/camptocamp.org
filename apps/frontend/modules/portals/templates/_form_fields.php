<?php
use_helper('Object', 'Language', 'Validation', 'MyForm');

$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));

// Here document = portal
echo '<div>';
display_document_edit_hidden_tags($document);
echo '</div>';
echo mandatory_fields_warning();

include_partial('documents/language_field', array('document'     => $document,
                                                  'new_document' => $new_document));

if (true or $is_moderator)
{
    echo object_group_tag($document, 'name', array('class' => 'long_input'));

    echo form_section_title('Information', 'form_info', 'preview_info');

    include_partial('documents/oam_coords', array('document' => $document));
    echo object_group_tag($document, 'elevation', array('suffix' => 'meters', 'class' => 'short_input'));
    echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                                   array('multiple' => true, 'na' => array(0)), true, null, null, '', '', 'picto_act act_');
    echo object_group_tag($document, 'has_map', array('callback' => 'object_checkbox_tag'));
    echo object_group_tag($document, 'map_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'topo_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_outings', array('class' => 'short_input'));
    echo object_group_tag($document, 'outing_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_images', array('class' => 'short_input'));
    echo object_group_tag($document, 'image_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_videos', array('class' => 'short_input'));
    echo object_group_tag($document, 'video_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_articles', array('class' => 'short_input'));
    echo object_group_tag($document, 'article_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_topics', array('class' => 'short_input'));
    echo object_group_tag($document, 'forum_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_news', array('class' => 'short_input'));
    echo object_group_tag($document, 'news_filter', array('class' => 'long_input'));
    echo object_group_tag($document, 'design_file', array('class' => 'long_input'));
}

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'abstract', null, array('no_img' => true, 'class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));

include_partial('documents/form_history');
?>
