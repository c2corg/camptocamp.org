<?php 
use_helper('Button', 'Field');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$id = $sf_params->get('id');
$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
?>

<nav id="nav_anchor" class="nav_box">
    <div id="nav_anchor_top"></div>
    <div id="nav_anchor_content">
        <ul>
            <li><?php echo button_anchor('Accident infos', 'data', 'action_informations', $module, $id, $lang); ?></li>
            <?php
            if ($section_list['map'])
            {
                echo li(button_anchor('Interactive map', 'map_container', 'picto_maps', $module, $id, $lang));
            }
            ?>
            <li><?php echo button_anchor('Accident description', 'description', 'action_description', $module, $id, $lang); ?></li>
            <li><?php echo button_anchor('Accident factors', 'factors', 'action_description', $module, $id, $lang); ?></li>
            <?php
            if ($is_connected && $is_moderator)
            {
                echo li(button_anchor('Profil', 'profil', 'picto_users', $module, $id, $lang));
            }
            ?>
            <li><?php echo button_anchor('Images', 'images', 'picto_images', $module, $id, $lang); ?></li>
        </ul>
    </div>
    <div id="nav_anchor_down"></div>
</nav>
