<?php
$item_i18n = $item->getRaw('ParkingI18n');
$item_i18n = $item_i18n[0];
?>
{
  "name": <?php echo json_encode($item_i18n['name']) ?>,
  "url": "<?php echo jsonlist_url($item_i18n, 'parkings') ?>",
  "elevation": <?php echo $item['elevation'] ?>,
  <?php if (is_scalar($item['lat'])): ?>
  "latitude": <?php echo $item['lat'] ?>,
  "longitude": <?php echo $item['lon'] ?>,
  <?php endif; if (isset($item['lowest_elevation']) && is_scalar($item['lowest_elevation'])): ?>
  "lowestElevation": <?php echo $item['lowest_elevation'] ?>,
  <?php endif; if (is_int($item['snow_clearance_rating']) && $item['snow_clearance_rating'] != 4): ?>
  "snowClearance": <?php echo $item['snow_clearance_rating'] ?>,
  <?php endif; if (check_not_empty($item->getRaw('public_transportation_types'))): ?>
  "publicTransportationTypes": <?php echo json_encode($item->getRaw('public_transportation_types')) ?>,
  <?php endif ?>
  "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
  "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
  "nbLinkedRoutes": <?php echo isset($item['nb_linked_docs']) ? $item['nb_linked_docs'] : 0 ?>,
  <?php
  include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations']));
  ?>
}
