<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        li(field_data_from_list($document, 'area_type', 'mod_areas_area_types_list')); 
        if ($sf_user->hasCredential('moderator') && $document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')));
        }
        ?>
    </ul>
</div>
