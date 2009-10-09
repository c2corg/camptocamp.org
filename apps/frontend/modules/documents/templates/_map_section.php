<?php 
$is_connected = $sf_user->isConnected();
$container_div = 'map_container';
$has_geom = (boolean)($document->get('geom_wkt'));
$state = ($has_geom) ? 'closed' : 'opened';

if ($is_connected || $has_geom)
{
    if ($has_geom)
    {
        echo start_section_tag('Interactive map', $container_div, $state, $has_geom);
    }

    include_partial('documents/maps', array(
        'document'          => $document,
        'search'            => false,
        'displayed_layers'  => $displayed_layers,
        'container_div'     => $container_div
    ));

    if ($has_geom)
    {
        echo end_section_tag($has_geom);
    }
}
?>
