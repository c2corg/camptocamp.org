<?php 
use_helper('Button', 'Ajax', 'Javascript');

$module = $sf_context->getModuleName();
$lang = $document->getCulture();
$has_rights = $sf_user->hasCredential('moderator');
$redirected = $document->get('redirects_to');
$is_archive = $document->isArchive();
$is_protected = $document->get('is_protected');
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
            <?php if ($has_rights && !$is_archive): ?>
                <li><?php echo button_delete($module, $id) ?></li>
                <li><?php echo button_delete_culture($module, $id, $document->get('culture')) ?></li>
            <?php endif ?>
            <?php if ($has_rights && !$is_archive && !$redirected && $document->get('geom_wkt')): ?>
                <li><?php echo button_delete_geom($module, $id) ?></li>
            <?php endif ?>
            <?php if ($has_rights && !$is_archive): ?>
                <li><?php echo button_clear_cache($module, $id) ?></li>
            <?php endif;
            /* we only give the possibility to refresh if the route has an attached gpx
               Else, it would erase geoassociations herited from the summits */
            if ($has_rights && !$is_archive && !$redirected && $document->get('geom_wkt')): ?>
                <li><?php echo button_refresh_geo_associations($module, $id) ?></li>
            <?php endif ?>
            <?php if ($sf_user->isConnected() && !$redirected && !$is_archive && !$is_protected): ?>
                <li><?php echo button_add_outing($id) ?></li>
            <?php endif ?>
            <li><?php echo button_rss($module, $lang, $id) ?></li>
            <li><?php echo button_report() ?></li>
            <li><?php echo button_help() ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</nav>
