<?php 
use_helper('Button');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
?>

<div id="nav_anchor">
    <div id="nav_anchor_top"></div>
    <div id="nav_anchor_content">
        <ul>
            <li><?php echo button_anchor('Personal information', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Interactive map', 'map_container', 'action_map', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('User outings', 'outings', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('User contributions', 'contributions', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Images', 'images', 'action_images', $module, $id, $lang); ?></li>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
