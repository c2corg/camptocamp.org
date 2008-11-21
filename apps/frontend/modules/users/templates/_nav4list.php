<?php use_helper('Button');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
?>

<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_changes($module) ?></li>
            <li><?php echo button_rsslist($module) ?></li>
            <?php if ($sf_user->isConnected()): ?>
                <li><?php echo button_rss($module, $lang) ?></li>
                <li><?php echo button_rss($module, $lang, null, 'creations') ?></li>
            <?php endif; ?>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
