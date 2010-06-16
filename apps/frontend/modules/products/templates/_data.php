<?php use_helper('Field'); ?>

    <ul id="article_gauche_5050" class="data">
        <?php
        li(field_data_from_list($document, 'product_type', 'mod_products_types_list', true));
        li(field_data($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_swiss_coords($document));
        li(field_url_data_if_set($document, 'url'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        }
        ?>
    </ul>
