<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('site');
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_data_from_list($document, 'site_types', 'app_sites_site_types', true));
        li(field_data_if_set($document, 'routes_quantity'));
        li(field_data_range_from_list_if_set($document, 'min_rating', 'max_rating', 'range separator', 'app_routes_rock_free_ratings'));
        li(field_data_from_list_if_set($document, 'mean_rating', 'app_routes_rock_free_ratings'));
        li(field_data_range_if_set($document, 'min_height', 'max_height', 'range separator', '', '', 'meters'));
        li(field_data_if_set($document, 'mean_height', '', 'meters'));
        li(field_data_from_list_if_set($document, 'equipment_rating', 'app_equipment_ratings_list'));
        li(field_data_from_list_if_set($document, 'climbing_styles', 'app_climbing_styles_list', true));
        li(field_data_from_list_if_set($document, 'rock_types', 'app_rock_types_list', true));
        li(field_data_from_list_if_set($document, 'children_proof', 'mod_sites_children_proof_list'));
        li(field_data_from_list_if_set($document, 'rain_proof', 'mod_sites_rain_proof_list'));
        li(field_data_from_list_if_set($document, 'facings', 'mod_sites_facings_list', true));
        li(field_months_data($document, 'best_periods'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        }
        ?>
    </ul>
</div>
