<?php 
use_helper('Field'); 

echo field_text_data_if_set($document, 'description', null,
                     array('needs_translation' => $needs_translation, 'images' => $images, 'show_label' => false));
echo field_text_data_if_set($document, 'remarks', null,
                            array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'gear', null,
                            array('needs_translation' => $needs_translation, 'images' => $images));
$inserted_text = '';
if (isset($associated_books))
{
    $inserted_text = format_book_data($associated_books, 'br', $main_id, $sf_user->hasCredential('moderator'),
                                      ($sf_user->isConnected() && !$document->get('is_protected')));
}
if (!$sf_user->isConnected())
{
     if (isset($associated_books))
    {
        echo field_text_data($document, 'external_resources', null, array('needs_translation' => $needs_translation,
                                                                          'inserted_text' => $inserted_text, 'images' => $images));
    }
    else
    {
        echo field_text_data_if_set($document, 'external_resources', null, array('needs_translation' => $needs_translation,
                                                                                 'inserted_text' => $inserted_text, 'images' => $images));
    }
}
else
{
    echo field_text_data($document, 'external_resources', null, array('needs_translation' => $needs_translation,
                                                                      'inserted_text' => $inserted_text, 'images' => $images));
}
echo field_text_data_if_set($document, 'route_history', null, array('needs_translation' => $needs_translation, 'images' => $images));
