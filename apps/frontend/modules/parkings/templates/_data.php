<?php use_helper('Field'); ?>

    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('parking');
        li(field_data($document, 'elevation', array('suffix' => 'meters')));
        if ($document->get('lowest_elevation') != $document->get('elevation') && $document->get('snow_clearance_rating') != 4)
        {
            li(field_data($document, 'lowest_elevation', array('suffix' => 'meters')));
        }
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_swiss_coords($document));
        li(field_data_from_list($document, 'public_transportation_rating', 'app_parkings_public_transportation_ratings'));
        li(field_pt_picto_if_set($document));
        if ($document->get('snow_clearance_rating') != 4)
        {
            li(field_data_from_list($document, 'snow_clearance_rating', 'mod_parkings_snow_clearance_ratings_list'));
        }
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')), true);
        }
        ?>
    </ul>
