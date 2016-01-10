<?php 
use_helper('Field','Button'); 

$activities = $document->getRaw('activities');
$global_rating = $document->getRaw('global_rating');
$configuration = $document->getRaw('configuration');
$equipment_rating = $document->getRaw('equipment_rating');
$is_on_glacier = $document->getRaw('is_on_glacier');
$backpack_content_list = array();
$backpack_content_links = array();

if (array_intersect(array(1,7), $activities))
{
    $backpack_content_list[] = 'pack_skitouring';
}
if (in_array(2, $activities))
{
    if ($global_rating <= 8)
    {
        $backpack_content_list[] = 'pack_snow_ice_mixed_easy';
    }
}
if (in_array(3, $activities))
{
    if ($global_rating <= 12)
    {
        $backpack_content_list[] = 'pack_mountain_climbing_easy';
    }
}
if (in_array(4, $activities))
{
    if ($equipment_rating >= 4 && $equipment_rating <= 6)
    {
        $backpack_content_list[] = 'pack_rock_climbing_bolted';
    }
    elseif ($equipment_rating > 6 && $global_rating <= 12 && !in_array(3, $activities))
    {
        $backpack_content_list[] = 'pack_mountain_climbing_easy';
    }
}
if (in_array(5, $activities))
{
    $backpack_content_list[] = 'pack_ice';
}
if (in_array(6, $activities))
{
    $backpack_content_list[] = 'pack_hiking';
}
if (array_intersect(array(1,2,3,7), $activities) && $is_on_glacier == 1)
{
    $backpack_content_list[] = 'glacier gear';
}

foreach ($backpack_content_list as $backpack_content)
{
    $link_text = __($backpack_content);
    if ($backpack_content == 'glacier gear')
    {
        $url = getMetaArticleRoute('pack_snow_ice_mixed_easy', false, 'glacier-gear');
    }
    else
    {
        $url = getMetaArticleRoute($backpack_content, false);
    }
    $backpack_content_links[] = '<li>' . link_to($link_text, $url) . '</li>';
}

if (count($backpack_content_links))
{
    $gear_inserted_text = '<ul class="text big_tips">'
                        . implode('', $backpack_content_links)
                        . "</ul>\n";
}
else
{
    $gear_inserted_text = '';
}

echo field_text_data_if_set($document, 'description', null,
                     array('needs_translation' => $needs_translation, 'images' => $images, 'show_label' => false));

$remarks = field_text_data_if_set($document, 'remarks', null,
                            array('needs_translation' => $needs_translation, 'show_images' => false));
$gear = field_text_data_if_set($document, 'gear', null,
                            array('needs_translation' => $needs_translation, 'show_images' => false, 'inserted_text' => $gear_inserted_text));

if (!empty($remarks) || !empty($gear))
{
    echo '<div class="clearer"></div>';
    
    echo '<div class="col_left col_66">';
    if (!empty($remarks))
    {
        echo $remarks;
    }
    else
    {
        echo $gear;
    }
    echo '</div>';
    
    if (!empty($remarks) && !empty($gear))
    {
        echo '<div class="col_right col_33">';
        echo $gear;
        echo '</div>';
    }
    
    echo '<div class="clearer"></div>';
}

$inserted_text = '';
if (isset($associated_books) && count($associated_books))
{
    $main_id = isset($main_id) ? $main_id : null;
    $inserted_text = format_book_data($associated_books, 'br', $main_id, $sf_user->hasCredential('moderator'));
}
echo field_text_data_if_set($document, 'external_resources', null, array('needs_translation' => $needs_translation,
                                                                         'inserted_text' => $inserted_text, 'images' => $images));

echo field_text_data_if_set($document, 'route_history', null, array('needs_translation' => $needs_translation, 'images' => $images));
