<?php
use_helper('Field');

echo field_text_data($document, 'description', null, $needs_translation);
echo field_text_data_if_set($document, 'remarks', null, $needs_translation);
echo field_text_data_if_set($document, 'pedestrian_access', null, $needs_translation);
echo field_text_data_if_set($document, 'way_back', null, $needs_translation);
$inserted_text = '';
if (isset($associated_books))
{
    $inserted_text = format_book_data($associated_books, 'bt', $main_id, $sf_user->hasCredential('moderator'),
                                      ($sf_user->isConnected() && !$document->get('is_protected')));
}
if (!$sf_user->isConnected())
{
    echo field_text_data_if_set($document, 'external_resources', null, $needs_translation, $inserted_text);
}
else
{
    echo field_text_data($document, 'external_resources', null, $needs_translation, $inserted_text);
}
echo field_text_data_if_set($document, 'site_history', null, $needs_translation);
