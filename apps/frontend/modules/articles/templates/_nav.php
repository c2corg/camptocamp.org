<?php 
use_helper('Button', 'Ajax', 'Javascript');

$module = $sf_context->getModuleName();
$id = $document->get('id');
$lang = $document->getCulture();
$has_rights = $sf_user->hasCredential('moderator');
$redirected = $document->get('redirects_to');
$is_archive = $document->isArchive();

$needs_delete_action = $has_rights && !$is_archive;
$needs_protect_action = $needs_delete_action && !$redirected;
$needs_merge_action = $needs_protect_action;
$needs_delete_geom_action = $needs_protect_action && $document->get('geom_wkt');
?>

<div id="nav_tools">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_back($module) ?></li>
            <?php if (!$is_archive): ?>
              <li><?php echo button_prev($module,$id); ?></li>
              <li><?php echo button_next($module,$id); ?></li>
            <?php endif ?>
            <li><?php echo button_search($module) ?></li>
            <li><?php echo button_print() ?></li>
            <?php if ($needs_protect_action): ?>
                <li><?php echo button_protect($module, $id, $document->get('is_protected'));?></li>
            <?php endif ?>
            <?php if ($needs_merge_action): ?>
                <li><?php echo button_merge($module, $id) ?></li>
            <?php endif ?>
            <?php if ($needs_delete_action): ?>
                <li><?php echo button_delete($module, $id) ?></li>
                <li><?php echo button_delete_culture($module, $id, $document->get('culture')) ?></li>
            <?php endif ?>
            <?php if ($needs_delete_geom_action): ?>
                <li><?php echo button_delete_geom($module, $id) ?></li>
            <?php endif ?>
            <?php if ($has_rights): ?>
                <li><?php echo button_clear_cache($module, $id) ?></li>
            <?php endif ?>
            <li><?php echo button_rss($module, $lang, $id) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help_articles') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
