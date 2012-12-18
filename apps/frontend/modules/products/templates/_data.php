<?php
use_helper('Field');
// put here meta tags for microdata which would be invalid inside ul tag
echo microdata_meta('name', $document->getName());
?>
<ul id="article_gauche_5050" class="data">
    <?php
    li(field_data_from_list($document, 'product_type', 'mod_products_types_list', true));
    if (check_not_empty_doc($document, 'elevation') || check_not_empty_doc($document, 'lon'))
    {
        echo '<li><ul itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        li(field_data_if_set($document, 'elevation', array('suffix' => 'meters', 'microdata' => 'elevation')));
        li(field_coord_data_if_set($document, 'lon', array('microdata' => 'longitude')));
        li(field_coord_data_if_set($document, 'lat', array('microdata' => 'latitude')));
        li(field_swiss_coords($document));
        echo '</ul></li>';
    }
    li(field_url_data_if_set($document, 'url', array('microdata' => 'url')));

    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')), true);
    }
    ?>
</ul>
