<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
    <?php
        disp_doc_type('map');
        li(field_data_from_list_if_set($document, 'editor', 'mod_maps_editors_list'));
        li(field_data_from_list_if_set($document, 'scale', 'mod_maps_scales_list'));
        li(field_data_if_set($document, 'code'));
    ?>
    </ul>
</div>
