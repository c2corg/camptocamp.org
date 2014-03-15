<?php
use_helper('Field');
$item_i18n = $item->getRaw('RouteI18n');
$item_i18n = $item_i18n[0];
// TODO #337 cf what is done in list_body
?>
{
  "type": "Feature",
  "geometry": <?php echo gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt')) ?>,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'routes') ?>",
    "hasTrack": <?php echo strlen($item['geom_wkt']) > 0 ? 'true' : 'false' ?>,
    "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
    "nbLinkedOutings": <?php echo isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0 ?>,
    "ratings": <?php echo json_encode(field_route_ratings_data($item, false, false, false, 'json')) ?>,
    <?php if (check_not_empty($item->getRaw('max_elevation'))):  ?>
    "maxElevation": <?php echo $item['max_elevation'] ?>,
    <?php endif; if (check_not_empty($item->getRaw('height_diff_up'))):  ?>
    "heightDiffUp": <?php echo $item['height_diff_up'] ?>,
    <?php endif; if (check_not_empty($item->getRaw('difficulties_height'))):  ?>
    "heightDiffUp": <?php echo $item['difficulties_height'] ?>,
    <?php endif ?>
    "facings": <?php echo $item['facing'] // TOD ?>
    <?php
    include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']));
    echo ',';
    include_partial('parkings/parkings4jsonlist', array('parkings' => (isset($item['linked_docs']) ? $item['linked_docs'] : array())));
    ?>
  }
}
