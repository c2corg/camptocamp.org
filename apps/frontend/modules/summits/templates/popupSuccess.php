<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();
$nb_routes = count($associated_routes);

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=summits&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'summits', $route);

$description = $document->getRaw('description');
if (!empty($description)) {
    $description = truncate_description($description, $route, 800, true);
} else {  
    $description = '';
}

$image = make_thumbnail_slideshow($associated_images);

if (!$raw && $image)
{
    echo insert_popup_js();
}

if ($description || $image):
    $desc_class = 'popup_desc';
    if ($nb_routes > 0)
    {
        $desc_class .= ' popup_iti';
    }
?>
<div class="<?php echo $desc_class ?>"><?php echo $image . $description; ?></div>
<?php endif;

echo make_routes_title(__('Linked routes'), $nb_routes);

if ($nb_routes)
{
    $routes_class = '';
    if (!$description && !$image)
    {
        $routes_class = ' class="full"';
    }
    echo '<div id="routes_section_container"' . $routes_class . '>';

    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'is_popup' => true,
                                                  'type' => 'sr', // route - summit, reversed
                                                  'external_links' => true,
                                                  'strict' => true));

    echo '</div>';
}

echo javascript_tag('C2C.init_popup();');
