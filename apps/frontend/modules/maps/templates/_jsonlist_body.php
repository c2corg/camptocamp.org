<?php
$item_i18n = $item->getRaw('MapI18n');
$item_i18n = $item_i18n[0];
?>
{
  "type": "Feature",
  "geometry": <?php echo gisQuery::EWKT2GeoJSON($item->getRaw('geom_wkt')) ?>,
  "properties": {
    "name": <?php echo json_encode($item_i18n['name']) ?>,
    "url": "<?php echo jsonlist_url($item_i18n, 'maps') ?>",
    "code": <?php echo json_encode($item['code']) ?>,
    "scale": <?php echo $item['scale'] ?>,
    "editor": <?php $a = sfConfig::get('mod_maps_editors_list'); echo json_encode($a[$item['editor']]) ?>,
    "nbLinkedImages": <?php echo isset($item['nb_images']) ?  $item['nb_images'] : 0 ?>,
    "nbComments": <?php echo isset($item['nb_comments']) ? $item['nb_comments'] : 0 ?>
  }
}
