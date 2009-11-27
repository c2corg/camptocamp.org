<?php
use_helper('Field');

echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images, 'show_label' => false));
echo field_text_data_if_set($document, 'remarks', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'pedestrian_access', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'way_back', null, array('needs_translation' => $needs_translation, 'images' => $images));

$inserted_text = '';
if (isset($associated_books) && count($associated_books))
{
    $main_id = isset($main_id) ? $main_id : null;
    $inserted_text = format_book_data($associated_books, 'bt', $main_id, $sf_user->hasCredential('moderator'));
}
echo field_text_data_if_set($document, 'external_resources', null, array('needs_translation' => $needs_translation,
                                                                         'inserted_text' => $inserted_text, 'images' => $images));

echo field_text_data_if_set($document, 'site_history', null, array('needs_translation' => $needs_translation, 'images' => $images));
