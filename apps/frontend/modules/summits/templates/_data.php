<?php use_helper('Field'); ?>

<div class="col_left col_33">
    <ul class="data">
        <?php
        disp_doc_type('summit');
        li(field_data($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_data_from_list($document, 'summit_type', 'app_summits_summit_types'));
        li(field_data_if_set($document, 'maps_info'));

        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        }
        ?>
    </ul>
