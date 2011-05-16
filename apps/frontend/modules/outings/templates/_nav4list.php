<?php use_helper('Button');
$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
?>

<nav id="nav_tools" class="nav_box">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <?php if ($sf_user->isConnected()): ?>
                <li><?php echo button_wizard(array('class'=>'action_create nav_edit')) ?></li>
            <?php endif ?>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_map($module) ?></li>
            <li><?php echo button_changes($module) ?></li>
            <?php if ($sf_context->getActionName() != 'conditions'): ?>
                <li><?php echo button_rsslist($module) ?></li>
            <?php endif ?>
            <li><?php echo button_rss($module, $lang) ?></li>
            <li><?php echo button_rss($module, $lang, null, 'creations') ?></li>
            <li><?php echo button_widget($sf_request->getParameterHolder()->getAll()) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help() ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</nav>
