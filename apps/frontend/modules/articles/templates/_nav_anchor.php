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
            <li><?php echo button_anchor('Article', 'description', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Information', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Linked documents', 'associated_docs', 'picto_documents', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Images', 'images', 'picto_images', $module, $id, $lang); ?></li>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
