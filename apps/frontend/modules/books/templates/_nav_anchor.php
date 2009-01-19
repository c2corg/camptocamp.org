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
            <?php
            echo li(button_anchor('Information', 'data', 'action_informations', $module, $id, $lang));
            echo li(button_anchor('Description', 'description', 'action_description', $module, $id, $lang));
            if ($section_list['routes'])
                echo li(button_anchor('Linked routes', 'linked_routes', 'action_routes', $module, $id, $lang));
            if ($section_list['summits'])
                echo li(button_anchor('Linked summits', 'linked_summits', 'action_summits', $module, $id, $lang));
            if ($section_list['huts'])
                echo li(button_anchor('Linked huts', 'linked_huts', 'action_huts', $module, $id, $lang));
            if ($section_list['sites'])
                li(button_anchor('Linked sites', 'linked_sites', 'action_sites', $module, $id, $lang));
            if ($section_list['docs'])
                li(button_anchor('Linked documents', 'associated_docs', 'action_documents', $module, $id, $lang));
            echo li(button_anchor('Images', 'images', 'action_images', $module, $id, $lang));
            ?>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</div>
