<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('hut');
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')));
        }

        li(field_data_from_list($document, 'shelter_type', 'mod_huts_shelter_types_list'));
        li(field_bool_data($document, 'is_staffed'));
        li(field_data_if_set($document, 'phone'));
        li(field_url_data_if_set($document, 'url'));
        li(field_data_if_set($document, 'staffed_capacity'));
        li(field_data_if_set($document, 'unstaffed_capacity'));
        li(field_bool_data($document, 'has_unstaffed_matress'));
        li(field_bool_data($document, 'has_unstaffed_blanket'));
        li(field_bool_data($document, 'has_unstaffed_gas'));
        li(field_bool_data($document, 'has_unstaffed_wood'));
        li(field_activities_data($document)); 
        ?>
    </ul>
</div>
