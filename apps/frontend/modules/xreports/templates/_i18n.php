<?php
use_helper('sfBBCode', 'SmartFormat', 'Field', 'Sections'); 

if (!isset($preview))
{
    $preview = false;
}

echo field_text_data_if_set($document, 'place', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'description', 'xreport_description', array('needs_translation' => $needs_translation, 'images' => $images, 'label_id' => 'xreport_description'));

?></div><?php
if ($preview)
{
    echo end_preview_section_tag();
    echo start_preview_section_tag('Accident factors', 'factors', 'factors');
}
else
{
    echo end_section_tag();
    echo start_section_tag('Accident factors', 'factors');
}
?><div class="article_contenu"><?php

echo field_text_data_if_set($document, 'route_study', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'conditions', 'xreport_conditions short', array('needs_translation' => $needs_translation, 'images' => $images, 'label_id' => 'xreport_conditions'));
echo field_text_data_if_set($document, 'training', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'motivations', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'group_management', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'risk', 'xreport_risk', array('needs_translation' => $needs_translation, 'show_images' => false, 'label_id' => 'xreport_risk'));
echo field_text_data_if_set($document, 'time_management', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'safety', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'reduce_impact', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'increase_impact', null, array('needs_translation' => $needs_translation, 'show_images' => false));
echo field_text_data_if_set($document, 'modifications', 'xreport_modifications', array('needs_translation' => $needs_translation, 'show_images' => false, 'label_id' => 'xreport_modifications'));
echo field_text_data_if_set($document, 'other_comments', 'xreport_other_comments', array('needs_translation' => $needs_translation, 'show_images' => false));
