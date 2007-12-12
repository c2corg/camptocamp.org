<?php use_helper('Button') ?>
<?php $module = $sf_context->getModuleName() ?>
<?php $lang = $sf_user->getCulture() ?>

<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_changes($module) ?></li>
            <li><?php echo button_rss($module, $lang) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
