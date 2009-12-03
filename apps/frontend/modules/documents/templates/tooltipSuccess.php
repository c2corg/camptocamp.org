<?php
$nb_features = count($items);
$i = 1;
?>
{
  "type": "FeatureCollection",
  "features": [
  <?php foreach($items as $feature): ?>
    {
      "type": "Feature",
      "geometry": {"type": "Point", "coordinates": [<?php echo $feature['lon'] . ', ' . $feature['lat']?>]},
      "properties": {
        "id": <?php echo $feature['id']; ?>,
        "layer": "<?php echo $feature['module']; ?>"
      }
    }<?php if ($i++ < $nb_features): ?>,<?php endif; ?>
  <?php endforeach; ?>
  ]
}
