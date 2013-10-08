<?php 
use_helper('Button', 'Ajax', 'Javascript', 'JavascriptQueue');

$module = $sf_context->getModuleName();
$lang = $document->getCulture();
$is_connected = $sf_user->isConnected();
$has_rights = $sf_user->hasCredential('moderator');
$redirected = $document->get('redirects_to');
$is_archive = $document->isArchive();
?>
<nav id="nav_tools" class="nav_box">
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
            <?php if ($has_rights && !$is_archive && !$redirected): ?>
                <li><?php echo button_protect($module, $id, $document->get('is_protected'));?></li>
                <li><?php echo button_merge($module, $id) ?></li>
            <?php endif ?>
            <?php if ($is_connected && !$is_archive && !$redirected): ?>
                <li><?php echo button_rotate(90, $id) ?></li>
                <li><?php echo button_rotate(-90, $id) ?></li>
                <?php if (!$has_rights && $document->get('image_type') == 2) // hide buttons if by-nc-nd and not author?
                {
                    echo javascript_queue('if ('.$creator_id.' != jQuery("#name_to_use").attr("data-user-id")) {
                      jQuery(".action_rotate").hide();
                    }');
                } ?>
            <?php endif ?>
            <?php if ($has_rights && !$is_archive): ?>
                <li><?php echo button_delete($module, $id) ?></li>
                <li><?php echo button_delete_culture($module, $id, $document->get('culture')) ?></li>
            <?php endif ?>
            <?php if ($has_rights && !$is_archive && !$redirected && $document->get('geom_wkt')): ?>
                <li><?php echo button_delete_geom($module, $id) ?></li>
            <?php endif ?>
            <?php if ($has_rights && !$is_archive): ?>
                <li><?php echo button_clear_cache($module, $id) ?></li>
                <li><?php echo button_associations_history($module, $id) ?></li>
            <?php endif ?>
            <?php if ($is_connected && !$redirected): ?>
                <li><?php echo button_rss($module, $lang, $id) ?></li>
            <?php endif ?>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help('help_images') ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</nav>
