<?php 
use_helper('Button', 'Field');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
?>

<div id="nav_anchor" class="nav_box">
    <div id="nav_anchor_top"></div>
    <div id="nav_anchor_content">
        <ul>
            <li><?php echo button_anchor('Information', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Interactive map', 'map', 'picto_maps', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Description', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Images', 'images', 'picto_images', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Comments', 'comments', 'action_comment', $module, $id, $lang); ?></li>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
