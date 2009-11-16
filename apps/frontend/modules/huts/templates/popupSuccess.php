<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=huts&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'huts');

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
$data = field_data_if_set($document, 'phone');
if (!empty($data))
{
    $data_list[] = $data;
}
$data = field_url_data_if_set($document, 'url', true, 'www');
if (!empty($data))
{
    $data_list[] = $data;
}
li(implode(' - ', $data_list));

$data_list = array();
$suffix = ' ' . __('bedding places');
$data = field_data_if_set($document, 'staffed_capacity', '', $suffix, __('staffed_capacity short') . __('&nbsp;:'));
if (!empty($data))
{
    $data_list[] = $data;
}
$data = field_data_if_set($document, 'unstaffed_capacity', '', $suffix, __('unstaffed_capacity short') . __('&nbsp;:'));
if (!empty($data))
{
    $data_list[] = $data;
}
li(implode(' - ', $data_list));
?>
</ul>
<?php

if ($description) {
    echo $description;
}
?></div>
<?php endif;

echo make_routes_title(__('Routes from this hut'), count($associated_routes), $description || $image);

if (count($associated_routes))
{
    echo '<div id="routes_section_container">';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'is_popup' => true,
                                                  'type' => 'hr', // route - hut, reversed
                                                  'strict' => true));

    echo '</div>';
}

echo make_c2c_link($route);
