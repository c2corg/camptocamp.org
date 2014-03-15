<?php
$item_i18n = $item->getRaw('AreaI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": <?php echo gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt')) ?>,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'areas') ?>",
    "type":  <?php echo $item['area_type'] ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>
  }
}
