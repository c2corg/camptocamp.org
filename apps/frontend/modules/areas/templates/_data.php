<?php use_helper('Field'); ?>

<div class="article_contenu">
    <ul id="article_gauche_5050" class="data">
        <?php 
        disp_doc_type('area');
        li(field_data_from_list($document, 'area_type', 'mod_areas_area_types_list'));
        ?>
    </ul>
</div>
