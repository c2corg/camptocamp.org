<?php use_helper('Field'); ?>
<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('parking');
        li(field_data($document, 'elevation', '', 'meters'));
        li(field_data_if_set($document, 'lowest_elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_data_from_list($document, 'public_transportation_rating', 'mod_parkings_public_transportation_ratings_list'));
        li(field_data_from_list($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
            if ($sf_user->isConnected())
            {
                li(field_getdirections($sf_params->get('id')));
            }
        }
        ?>
    </ul>
</div>
