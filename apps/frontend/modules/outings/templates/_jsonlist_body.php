<?php
use_helper('Field');
$item_i18n = $item->getRaw('OutingI18n');
$item_i18n = $item_i18n[0];
?>
{
  "name": <?php echo json_encode($item_i18n['name']) ?>,
  "url": "<?php echo jsonlist_url($item_i18n, 'outings') ?>",
  "hasTrack": <?php echo strlen($item['geom_wkt']) > 0 ? 'true' : 'false' ?>,
  "activities": <?php echo json_encode(BaseDocument::convertStringToArray($item['activities'])) ?>,
  "creator": <?php echo json_encode($item['creator']) ?>,
  "date": <?php echo json_encode($item['date']) ?>,
  <?php if (check_not_empty($item->getRaw('max_elevation'))):  ?>
  "maxElevation": <?php echo $item['max_elevation'] ?>,
  <?php endif; if (check_not_empty($item->getRaw('height_diff_up'))): ?>
  "heightDiffUp": <?php echo $item['height_diff_up'] ?>,
  <?php endif; if (isset($item['linked_routes'])): ?>
  "routes_rating": <?php echo json_encode(field_route_ratings_data($item, false, false, false, 'json')) ?>,
  <?php endif; if (is_int($item['conditions_status'])): ?>
  "conditions": <?php echo $item['conditions_status'] ?>,
  <?php endif; if (isset($item['frequentation'])): ?>
  "frequentation": <?php echo $item['frequentation'] ?>,
  <?php endif ?>
  "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
  "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>,
  <?php include_partial('documents/regions4jsonlist', array('geoassociations' => $item['geoassociations'])) ?>
}
