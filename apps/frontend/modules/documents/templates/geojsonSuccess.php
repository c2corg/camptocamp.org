<?php
$items = $pager->getResults('array', ESC_RAW);
$items = Language::parseListItems($items, 'Summit');
/*
print '<pre>';
print_r($items);
print '</pre>';
*/
$nb_features = count($items);
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
        "name": "<?php echo $feature['SummitI18n'][0]['name']; ?>",
        "elevation": <?php echo $feature['elevation']; ?>
      }
    }<?php if ($i++ < $nb_features): ?>,<?php endif; ?>
  <?php endforeach; ?>
  ]
}
