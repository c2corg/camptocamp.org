<?php 
use_helper('Button', 'Field');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
?>

<div id="nav_anchor">
    <div id="nav_anchor_top"></div>
    <div id="nav_anchor_content">
        <ul>
            <li><?php echo button_anchor('Image', 'view', 'picto_images', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Description', 'description', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Information', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked documents', 'associated_docs', 'picto_documents', $module, $id, $lang); ?></li>
            <?php
            if ($section_list['map'])
            {
                echo li(button_anchor('Interactive map', 'map_container', 'picto_maps', $module, $id, $lang));
            }
            if ($section_list['images'])
            {
                echo li(button_anchor('Images', 'images', 'picto_images', $module, $id, $lang));
            }
            ?>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
