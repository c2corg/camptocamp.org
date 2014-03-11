<?php
// note: it would be great to use geoPHP, but unfortunately it doesn't support 3D nor time at the moment
$module = $sf_params->get('module');

function make_points($points)
{
    $a = array();
    foreach ($points as $point)
    {
        $_point = explode(' ', trim(str_replace(array('(', ')'), '', $point)));
        $p = array(floatval($_point[0]), floatval($_point[1]));
        if (count($_point) > 2)
        {
          $p[] = (abs($_point[2]) < 1) ? '0' : round($_point[2]);
        }
        $a[] = $p;
    }
    return $a;
}

$polygons = array_map(function($v)
{
    return array_map(function ($v)
    {
        return make_points(explode(',', $v));
    }, explode('),(', $v));
}, explode(')),((', $sf_data->getRaw('points')));

$id = $sf_params->get('id');

if (in_array($module, array('maps', 'areas')))
{
    $type = 'MultiPolygon';
    $geom = $polygons;
}
else
{
    $lines = $polygons[0];
    $nblines = count($lines);
    if ($nblines > 1) // route with multiple lines
    {
        $type = 'MultiLineString';
        $geom = $lines;
    }
    elseif (count($lines[0]) > 1) // route or outings, simple line
    {
        $type = 'LineString';
        $geom =  $lines[0];
    }
    elseif (count($lines[0]) === 1) // single point
    {
        $type = 'Point';
        $geom = $lines[0][0];
    }
}

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => array(
        'type' => $type,
        'coordinates' => $geom
    ),
    'properties' => array(
        'module' => $module,
        'id' => $id,
        'culture' => $sf_params->get('lang'),
        'name' => $sf_data->getRaw('name'),
        'description' => $sf_data->getRaw('description')
    )
));
