<?php 
$points = $sf_data->getRaw('points');
$nbpts = count($points);
$id = $sf_params->get('id');
?>
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" creator="camptocamp.org" version="1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
  <metadata>
    <link href="http://www.camptocamp.org">
      <text>Camptocamp.org</text>
    </link>
  </metadata>
<?php if ($nbpts > 1): ?>
  <trk>
    <name><?php echo $sf_data->getRaw('name') ?></name>
    <trkseg>
<?php 
$minlat = 180;  $minlon = 180;  $maxlat = 0; $maxlon = 0;
foreach ($points as $point): ?>
<?php $_point = explode(' ', trim($point)); 
$nb_fields = count($_point); 
$lon = number_format($_point[0], 6, '.', '');
$lat = number_format($_point[1], 6, '.', '');
$minlon = min($minlon, $lon);
$minlat = min($minlat, $lat);
$maxlon = max($maxlon, $lon);
$maxlat = max($maxlat, $lat);
?>
      <trkpt lat="<?php echo $lat ?>" lon="<?php echo $lon ?>"> 
<?php if ($nb_fields > 2): ?>
        <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
<?php if ($nb_fields == 4 && $_point[3]!=0): ?>
        <time><?php echo date('c', $_point[3]) ?></time>
<?php endif ?>
      </trkpt>
<?php endforeach ?>
    </trkseg>
  </trk>
  <bounds minlat="<?php echo $minlat ?>" minlon="<?php echo $minlon ?>" maxlat="<?php echo $maxlat ?>" maxlon="<?php echo $maxlon ?>"/>
<?php elseif ($nbpts = 1): ?>
<?php $_point = explode(' ', trim($points[0]));
        $lon = number_format($_point[0], 6, '.', '');
        $lat = number_format($_point[1], 6, '.', '');
?>
  <bounds minlat="<?php echo $lat ?>" minlon="<?php echo $lon ?>" maxlat="<?php echo $lat ?>" maxlon="<?php echo $lon ?>"/>
  <wpt lat="<?php echo $lat ?>" lon="<?php echo $lon ?>">
<?php if (count($_point) > 2): ?>
    <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
    <name><?php echo $sf_data->getRaw('name') ?></name>
  </wpt>
<?php endif ?>
</gpx>