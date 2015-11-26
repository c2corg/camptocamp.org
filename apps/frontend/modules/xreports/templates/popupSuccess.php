<?php 
use_helper('Popup');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . '&nbsp;m';
$route = "@document_by_id_lang_slug?module=xreports&id=$id&lang=$lang&slug=" . get_slug($document);

echo make_popup_title($title, 'xreports', $route);

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
?>
<div class="<?php echo $desc_class ?>"><?php echo $image . $description; ?></div>
<?php endif;
echo javascript_tag('C2C.init_popup();');
