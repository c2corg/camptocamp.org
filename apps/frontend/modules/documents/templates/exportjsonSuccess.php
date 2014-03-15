<?php
$module = $sf_params->get('module');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode($sf_data->getRaw('geojson')),
    'properties' => array(
        'module' => $module,
        'id' => $sf_params->get('id'),
        'culture' => $sf_params->get('lang'),
        'name' => $sf_data->getRaw('name'),
        'description' => $sf_data->getRaw('description')
    )
));
