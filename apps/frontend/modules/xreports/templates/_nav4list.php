<?php
use_helper('Button');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
?>

<nav id="nav_tools" class="nav_box">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_create($module) ?></li>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_map($module) ?></li>
            <li><?php echo button_changes($module) ?></li>
            <li><?php echo button_rsslist($module) ?></li>
            <li><?php echo button_rss($module, $lang) ?></li>
            <li><?php echo button_rss($module, $lang, null, 'creations') ?></li>
            <li><?php echo button_widget($sf_request->getParameterHolder()->getAll()) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help() ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</nav>
