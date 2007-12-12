<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('summit');
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_data_if_set($document, 'maps_info'));

        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')));
        }

        li(field_data_from_list($document, 'summit_type', 'mod_summits_summit_types_list'));
        ?>
    </ul>
</div>
