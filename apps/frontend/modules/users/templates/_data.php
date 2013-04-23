<?php use_helper('Field'); ?>

<ul id="article_gauche_5050" class="data">
    <?php
    disp_doc_type('user');
    disp_nickname(html_entity_decode($forum_nickname));
    if ($forum_moderator && $topoguide_moderator)
    {
        disp_moderator('forum topoguide moderator');
    }
    else if ($forum_moderator && !$topoguide_moderator)
    {
        disp_moderator('forum moderator');
    }
    else if (!$forum_moderator && $topoguide_moderator)
    {
        disp_moderator('topoguide moderator');
    }

    li(field_activities_data_if_set($document));
    li(field_data_from_list_if_set($document, 'category', 'mod_users_category_list'));
    
    li(field_coord_data_if_set($document, 'lon'), array('class' => 'separator'));
    li(field_coord_data_if_set($document, 'lat'));
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')));
        li(field_getdirections($sf_params->get('id')));
    }
    ?>
</ul>
