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
    echo object_group_tag($document, 'name', null, '', array('class' => 'long_input'));

    echo form_section_title('Information', 'form_info', 'preview_info');

    include_partial('documents/oam_coords', array('document' => $document));
    echo object_group_tag($document, 'elevation', null, 'meters', array('class' => 'short_input'));
    echo object_group_dropdown_tag($document, 'activities', 'app_activities_list',
                                   array('multiple' => true, 'na' => array(0)), true, null, null, '', '', 'picto_act act_');
    echo object_group_tag($document, 'has_map', 'object_checkbox_tag');
    echo object_group_tag($document, 'map_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'topo_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_outings', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'outing_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_images', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'image_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_videos', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'video_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_articles', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'article_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_topics', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'forum_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'nb_news', null, '', array('class' => 'short_input'));
    echo object_group_tag($document, 'news_filter', null, '', array('class' => 'long_input'));
    echo object_group_tag($document, 'design_file', null, '', array('class' => 'long_input'));
}

echo form_section_title('Description', 'form_desc', 'preview_desc');

echo object_group_bbcode_tag($document, 'abstract', null, array('no_img' => true, 'class' => 'smalltext'));
echo object_group_bbcode_tag($document, 'description', null, array('class' => 'largetext'));

include_partial('documents/form_history');
?>
