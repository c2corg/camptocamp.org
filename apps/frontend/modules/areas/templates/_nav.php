<?php 
use_helper('Button', 'Ajax', 'Javascript');

$module = $sf_context->getModuleName();
$lang = $sf_user->getCulture();
$has_rights = $sf_user->hasCredential('moderator');
$redirected = $document->get('redirects_to');
$is_archive = $document->isArchive();

$needs_protect_action = $has_rights && !$is_archive && !$redirected;
?>
<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_back($module) ?></li>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_print() ?></li>
            <?php if ($needs_protect_action): ?>
                <li><?php echo button_protect($module, $id, $document->get('is_protected'));?></li>
            <?php endif ?>
            <?php if ($has_rights): ?>
                <li><?php echo button_clear_cache($module, $id) ?></li>
            <?php endif ?>
            <li><?php echo button_rss($module, $lang, $id) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help() ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
