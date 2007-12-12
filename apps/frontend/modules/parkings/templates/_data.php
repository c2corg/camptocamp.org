<?php use_helper('Field'); ?>
<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('parking');
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_data_if_set($document, 'lowest_elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')));
        }
        
        li(field_data_from_list_if_set($document, 'public_transportation_rating', 'mod_parkings_public_transportation_ratings_list'));
        li(field_data_from_list_if_set($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list'));
        ?>
    </ul>
</div>
