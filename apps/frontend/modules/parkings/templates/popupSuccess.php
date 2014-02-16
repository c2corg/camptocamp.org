<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();
$nb_routes = count($associated_routes);

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=parkings&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'parkings', $route);

$data_list = array();

if ($document->get('snow_clearance_rating') != 4)
{
    $data = array();
    if ($document->get('lowest_elevation') != $document->get('elevation'))
    {
        $data_temp = field_data_if_set($document, 'lowest_elevation', array('suffix' => 'meters'));
        if (!empty($data_temp))
        {
            $data[] = $data_temp;
        }
    }
    $data_temp = field_data_from_list_if_set($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list', array('raw' => true));
    if (!empty($data_temp))
    {
        $data[] = $data_temp;
    }
    $data = implode(' - ', $data);
    if (!empty($data))
    {
        $data_list[] = $data;
    }
}

$data = field_data_from_list_if_set($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings', array('title' => __('public_transportation_rating short')));
$data .= field_pt_picto_if_set($document, true, ' - ');
if (!empty($data))
{
    $data_list[] = $data;
}

$description = $document->getRaw('public_transportation_description');
if (!empty($description))
{
    $description = truncate_description($description, $route);
}
else
{  
    $description = '';
}

$image = make_thumbnail_slideshow($associated_images);

if (!$raw && $image)
{
    echo insert_popup_js();
}

if (!empty($data_list) || $description || $image):
$class = 'popup_desc';
if ($nb_routes)
{
    $class .= ' popup_iti';
    $routes_class = '';
    if (!empty($data_list) && !$description && !$image)
    {
        $class .= ' small';
        $routes_class = ' class="large"';
    }
}
?>
<div class="<?php echo $class ?>"><?php
if ($image)
{
    echo $image;
}
if (!empty($data_list))
{
    echo '<ul class="data">';
    foreach($data_list as $data)
    {
        li($data);
    }
    echo '</ul>';
}
if ($description)
{
    echo $description;
}
?></div>
<?php endif;

echo make_routes_title(__('Linked routes'), $nb_routes);

if ($nb_routes)
{
    if (empty($data_list) && !$description && !$image)
    {
        $routes_class = ' class="full"';
    }
    echo '<div id="routes_section_container"' . $routes_class . '>';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'is_popup' => true,
                                                  'type' => 'pr', // route - parking, reversed
                                                  'external_links' => true,
                                                  'strict' => true));

    echo '</div>';
}

echo javascript_tag('C2C.init_popup();');
