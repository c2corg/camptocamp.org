<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        li(field_data($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_data_from_list($document, 'shelter_type', 'mod_huts_shelter_types_list'));
        li(field_bool_data($document, 'is_staffed', true));
        li(field_data_if_set($document, 'staffed_capacity'));
        li(field_data_if_set($document, 'unstaffed_capacity'));
        li(field_bool_data($document, 'has_unstaffed_matress', true));
        li(field_bool_data($document, 'has_unstaffed_blanket', true));
        li(field_bool_data($document, 'has_unstaffed_gas', true));
        li(field_bool_data($document, 'has_unstaffed_wood', true));
        li(field_data_if_set($document, 'phone'));
        li(field_url_data_if_set($document, 'url'));
        li(field_activities_data($document)); 
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')), true);
        }
        ?>
    </ul>
</div>
