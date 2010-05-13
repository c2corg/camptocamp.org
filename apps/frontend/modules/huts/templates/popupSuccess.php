<?php 
use_helper('Popup', 'Field');

$id = $sf_params->get('id');
$lang = $document->getCulture();
$nb_routes = count($associated_routes);

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=huts&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'huts', $route);

$data_list = $data = array();
$data_temp = field_data_if_set($document, 'phone');
if (!empty($data_temp))
{
    $data[] = $data_temp;
}
$data_temp = field_url_data_if_set($document, 'url', true, 'www');
if (!empty($data_temp))
{
    $data[] = $data_temp;
}
if (!empty($data))
{
    $data_list[] = implode(' - ', $data);
}

$data = array();
$suffix = ' ' . __('bedding places');
$data_temp = field_data_if_set($document, 'staffed_capacity', '', $suffix, __('staffed_capacity short') . __('&nbsp;:'));
if (!empty($data_temp))
{
    $data[] = $data_temp;
}
$data_temp = field_data_if_set($document, 'unstaffed_capacity', '', $suffix, __('unstaffed_capacity short') . __('&nbsp;:'));
if (!empty($data_temp))
{
    $data[] = $data_temp;
}
if (!empty($data))
{
    $data_list[] = implode(' - ', $data);
}

$description = $document->getRaw('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {   
    $description = ''; 
}

$image = make_thumbnail_slideshow($associated_images);

if (!$raw && ($image || $nb_routes))
{
    echo insert_popup_js();
}

if (!empty($data_list) || $description || $image):
$class = 'popup_desc';
if ($nb_routes)
{
    $class .= ' popup_iti';
}
?>
<div class="<?php echo $class ?>"><?php
if ($image)
{
    echo $image;
    echo javascript_tag('init_slideshow();');
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

echo make_routes_title(__('Routes from this hut'), $nb_routes);

if ($nb_routes)
{
    echo '<div id="routes_section_container">';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'external_links' => true,
                                                  'is_popup' => true));
    echo '</div>';
}

//echo make_c2c_link($route, $nb_routes && ($description || $image), $raw);
