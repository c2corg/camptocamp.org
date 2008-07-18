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
            <li><?php echo button_anchor('Information', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Description', 'description', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked summits', 'linked_summits', 'action_summits', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked routes', 'linked_routes', 'action_routes', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked huts', 'linked_huts', 'action_huts', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked sites', 'linked_sites', 'action_sites', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Images', 'images', 'action_images', $module, $id, $lang); ?></li>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
