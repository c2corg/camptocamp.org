<?php
$module = $sf_params->get('module');

echo json_encode(array(
    'type' => 'Feature',
    'geometry' => json_decode($sf_data->getRaw('geojson')),
    'id' => $sf_params->get('id'),
    'properties' => array(
        'module' => $module,
        'culture' => $sf_params->get('lang'),
        'name' => $sf_data->getRaw('name'),
        'description' => $sf_data->getRaw('description')
    )
), JSON_PRETTY_PRINT | JSON_UNESCAPE_SLASHES);
