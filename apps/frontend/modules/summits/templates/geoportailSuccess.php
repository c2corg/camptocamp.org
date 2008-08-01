<?php 
use_helper('Geoportail');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . ' m';
$route = "@document_by_id_lang?module=summits&id=$id&lang=$lang";

echo make_gp_title($title, 'summits');

$description = $document->get('description');
if (!empty($description)) {
    $description = truncate_description($description, $route);
} else {  
    $description = '';
}

$image = formate_thumbnail($associated_images);

if ($description || $image):
?>
<p><?php echo $image . $description; ?></p>
<?php endif; ?>

<h4>Itinéraires associés :</h4>

<?php
include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                              'document' => $document,
                                              'type' => 'sr', // route - summit, reversed
                                              'strict' => true));

echo make_c2c_link($route);
