<?php
$items = $pager->getResults('array', ESC_RAW);
$nb_features = count($items);

if ($nb_features > 0) {
    $items = Language::parseListItems($items, 'Summit');
}

$i = 1;

// TODO: set features properties/geometries in partials dedicated to the current module
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
        "module": "<?php echo $feature['module']; ?>",
        "name": "<?php echo $feature['SummitI18n'][0]['name']; ?>",
        "elevation": <?php echo $feature['elevation']; ?>
      },
      "id": <?php echo $feature['id']; ?>
    }<?php if ($i++ < $nb_features): ?>,<?php endif; ?>
  <?php endforeach; ?>
  ]
}
