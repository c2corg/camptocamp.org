<?php 
$points = $sf_data->getRaw('points');
$nbpts = count($points);
$id = $sf_params->get('id');
?>
{"type": "Feature", "geometry": <?php if ($nbpts > 1): ?>{"type": "LineString", "coordinates": [[<?php 
    $a = array(); 
    foreach ($points as $point)
    {
        $_point = explode(' ', trim($point)); 
        $ll = number_format($_point[0], 6, '.', '').', '. number_format($_point[1], 6, '.', '');
        $a[] = (count($_point) > 2) ? $ll . ', ' . ((abs($_point[2])<1) ? '0' : round($_point[2])) : $ll;
    }
    echo implode('], [', $a) ?>]]}<?php elseif ($nbpts = 1): $_point = explode(' ', trim($points[0])); ?>{"type": "Point", "coordinates": [<?php echo number_format($_point[0], 6, '.', '') ?>, <?php echo number_format($_point[1], 6, '.', ''); if (count($_point) > 2): ?>, <?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif ?>]}<?php endif ?>, "properties": { "module": "<?php echo $sf_context->getModuleName() ?>", "id": <?php echo $id ?>, "culture": "<?php echo $sf_params->get('lang') ?>", "name": <?php echo json_encode($sf_data->getRaw('name')) ?>, "description": <?php echo json_encode($sf_data->getRaw('description')) ?>}}