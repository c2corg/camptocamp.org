<?php 
use_helper('Geoportail');

$id = $sf_params->get('id');
$lang = $document->getCulture();

$title = $document->get('name') . ' - ' . $document->get('elevation') . ' m';
$route = "@document_by_id_lang?module=huts&id=$id&lang=$lang";

echo make_gp_title($title, 'huts');
?>

<ul class="data">
<?php
li(field_data_if_set($document, 'phone'));
li(field_url_data_if_set($document, 'url'));
li(field_data_if_set($document, 'staffed_capacity'));
li(field_data_if_set($document, 'unstaffed_capacity'));
?>
</ul>

<h4>Itinéraires au départ de ce refuge :</h4>

<?php
include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                              'document' => $document,
                                              'type' => 'hr', // hut - summit, reversed
                                              'strict' => true));

echo make_c2c_link($route);
