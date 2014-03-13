<?php
// gpx doesn't support polygons, so we transform everything into (multiple) lines
function make_points($string)
{
    $string = str_replace(array(')', '('), '', $string);
    $points = explode(',', $string);
    return array_map(function($point)
    {
        $a = array();
        $_point = explode(' ', trim($point));
        $a['lon'] = number_format($_point[0], 6, '.', '');
        $a['lat'] = number_format($_point[1], 6, '.', '');
        if (count($_point) > 2 && abs($_point[2]) > 0)
        {
            $a['ele'] = round($_point[2]);
        }
        if (count($_point) > 3 && $_point[3] != 0)
        {
            $a['time'] = date('c', $_point[3]);
        }
        return $a;
    }, $points);
}

$lines = array_map('make_points', explode('),(', $sf_data->getRaw('points')));
$id = $sf_params->get('id');
?>
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<gpx xmlns="http://www.topografix.com/GPX/1/1" creator="camptocamp.org" version="1.1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
  <metadata>
    <link href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/$id/" . $sf_params->get('lang') . "/$slug"; ?>">
      <text>Camptocamp.org</text>
    </link>
  </metadata>
  <?php if (count($lines) === 1 && count($lines[0]) === 1): /////// use a waypoint ////////// ?>
  <wpt lat="<?php echo $lines[0][0]['lat'] ?>" lon="<?php echo $lines[0][0]['lon'] ?>">
    <?php if (isset($lines[0][0]['ele'])): ?>
    <ele><?php echo $lines[0][0]['ele'] ?></ele>
    <?php endif ?>
    <name><?php echo $sf_data->getRaw('name') ?></name>
  </wpt>
  <?php else: /////// use track or route, depending on module ////////
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
  foreach ($lines as $points): ?>
  <<?php echo $line_tag ?>>
    <name><?php echo $sf_data->getRaw('name') ?></name>
    <?php if (isset($inner_tag)) echo "<$inner_tag>\n"; ?>
      <?php foreach ($points as $point): ?>
      <<?php echo $point_tag ?> lat="<?php echo $point['lat'] ?>" lon="<?php echo $point['lon'] ?>"> 
        <?php if (isset($point['ele'])): ?>
        <ele><?php echo $point['ele'] ?></ele>
        <?php endif ?>
        <?php if (isset($point['time'])): ?>
        <time><?php echo $point['time'] ?></time>
        <?php endif ?>
      </<?php echo $point_tag ?>>
      <?php endforeach ?>
    <?php if (isset($inner_tag)) echo "</$inner_tag>\n"; ?>
  </<?php echo $line_tag ?>>
  <?php endforeach ?>
<?php endif ?>
</gpx>
