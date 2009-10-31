<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=summits&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_gp_title($title, 'summits');

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
<div class="<?php echo $class ?>"><?php echo $image . $description; ?></div>
<?php endif;

echo make_routes_title(__('Linked routes'), count($associated_routes), $description || $image);

if (count($associated_routes))
{
    echo '<div id="routes_section_container">';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'is_popup' => true,
                                                  'type' => 'sr', // route - summit, reversed
                                                  'strict' => true));

    echo '</div>';
}

echo make_c2c_link($route);
