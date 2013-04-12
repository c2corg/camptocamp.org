<?php
// TODO
// for now we only support single lines, but in the future we might support multilines
// in that case each line would correspond to one <rte> (for routes)
//                                         to one <trkseg> in the only <trk> (for outings)
$points = $sf_data->getRaw('points');
$nbpts = count($points);
$id = $sf_params->get('id');
?>
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" creator="camptocamp.org" version="1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
  <metadata>
    <link href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/$id/" . $sf_params->get('lang') . "/$slug"; ?>">
      <text>Camptocamp.org</text>
    </link>
  </metadata>
<?php
if ($nbpts > 1): /////// use track or route, depending on module //////// 
switch ($sf_context->getModuleName())
{
    case 'routes':
        $line_tag = 'rte';
        $point_tag = 'rtept';
        $inner_tag = null;
        break;
    case 'outings':
    default:
        $line_tag = 'trk';
        $point_tag = 'trkpt';
        $inner_tag = 'trkseg';
}
?>
  <<?php echo $line_tag ?>>
    <name><?php echo $sf_data->getRaw('name') ?></name>
    <?php if (isset($inner_tag)) echo "<$inner_tag>\n"; ?>
<?php 
foreach ($points as $point): ?>
<?php $_point = explode(' ', trim($point)); 
$nb_fields = count($_point); 
$lon = number_format($_point[0], 6, '.', '');
$lat = number_format($_point[1], 6, '.', '');
?>
      <<?php echo $point_tag ?> lat="<?php echo $lat ?>" lon="<?php echo $lon ?>"> 
<?php if ($nb_fields > 2): ?>
        <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
<?php if ($nb_fields == 4 && $_point[3]!=0): ?>
        <time><?php echo date('c', $_point[3]) ?></time>
<?php endif ?>
      </<?php echo $point_tag ?>>
<?php endforeach ?>
    <?php if (isset($inner_tag)) echo "</$inner_tag>\n"; ?>
  </<?php echo $line_tag ?>>
<?php elseif ($nbpts = 1): /////// use a waypoint ////////// ?>
<?php $_point = explode(' ', trim($points[0]));
        $lon = number_format($_point[0], 6, '.', '');
        $lat = number_format($_point[1], 6, '.', '');
?>
  <wpt lat="<?php echo $lat ?>" lon="<?php echo $lon ?>">
<?php if (count($_point) > 2): ?>
    <ele><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]) ?></ele>
<?php endif ?>
    <name><?php echo $sf_data->getRaw('name') ?></name>
  </wpt>
<?php endif ?>
</gpx>
