<?php 
use_helper('Popup', 'Field');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=parkings&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'parkings');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {  
    $description = '';
}

$image = formate_thumbnail($associated_images);

if ($image || count($associated_routes))
{
    echo insert_popup_js();
}

if ($description || $image):
$class = 'gp_desc';
if (count($associated_routes))
{
    $class .= ' gp_iti';
}
?>
<div class="<?php echo $class ?>"><?php
if ($image) {
    echo $image;
}
?>
<ul class="data">
<?php
$data_list = array();
if ($document->get('lowest_elevation') != $document->get('elevation') && $document->get('snow_clearance_rating') != 4)
{
    $data = field_data($document, 'lowest_elevation', '', 'meters');
    if (!empty($data))
    {
        $data_list[] = $data;
    }
}
$data = field_data_from_list_if_set($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings');
if (!empty($data))
{
    $data_list[] = $data;
}
$data = field_pt_picto_if_set($document);
if (!empty($data))
{
    $data_list[] = $data;
}
$data = field_data_from_list($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list');
if (!empty($data))
{
    $data_list[] = $data;
}
foreach($data_list as $data)
{
    li($data);
}
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php endif;

echo make_routes_title(__('Linked routes'), count($associated_routes), $description || $image);

if (count($associated_routes))
{
    echo '<div id="routes_section_container">';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'is_popup' => true,
                                                  'type' => 'pr', // route - parking, reversed
                                                  'strict' => true));

    echo '</div>';
}

echo make_c2c_link($route);