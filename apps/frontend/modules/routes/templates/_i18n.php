<?php 
use_helper('Field'); 

echo field_text_data($document, 'description', null, $needs_translation);
echo field_text_data_if_set($document, 'remarks', null, $needs_translation);
echo field_text_data_if_set($document, 'gear', null, $needs_translation);
if (count($associated_books))
{
    $inserted_text = format_book_data($associated_books, $route_id, $sf_user->hasCredential('moderator'), ($sf_user->isConnected() && !$document->get('is_protected')));
    echo field_text_data($document, 'external_resources', null, $needs_translation, $inserted_text);
} else {
    echo field_text_data_if_set($document, 'external_resources', null, $needs_translation);
}
echo field_text_data_if_set($document, 'route_history', null, $needs_translation);
