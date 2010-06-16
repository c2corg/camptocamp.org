<?php use_helper('Field'); ?>

    <ul id="article_gauche_5050" class="data">
        <?php
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_swiss_coords($document));
        li(field_activities_data($document)); 
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        }
        ?>
    </ul>
