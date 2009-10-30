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
    insert_popup_js();
}

if ($description || $image):
$class = 'gp_desc';
if (count($associated_routes))
{
    $class .= 'gp_iti';
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
$data = field_data_if_set($document, 'staffed_capacity', '', $suffix, __('staffed_capacity short') . __(' :'));
if (!empty($data))
{
    $data_list[] = $data;
}
$data = field_data_if_set($document, 'unstaffed_capacity', '', $suffix, __('unstaffed_capacity short') . __(' :'));
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

<h4 id="routes_title"><?php
if (count($associated_routes))
{
    echo __('Routes from this hut');
    
    if ($description || $image)
    {
        echo '<span id="size_ctrl">'
           . picto_tag('picto_close', __('Reduce the list'),
                       array('class' => 'click', 'id' => 'close_routes'))
           . picto_tag('picto_open', __('Enlarge the list'),
                       array('class' => 'click', 'id' => 'open_routes'))
           . '</span>';
    }
}
else
{
    echo __('No linked route');
}
?></h4>

<?php
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
