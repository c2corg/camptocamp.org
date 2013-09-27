<?php use_helper('Field');
// put here meta tags for microdata which would be invalid inside ul tag
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=sites&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul id="article_gauche_5050" class="data">
    <?php
    li(field_data_from_list($document, 'site_types', 'app_sites_site_types', array('multiple' => true)));
    if (check_not_empty_doc($document, 'elevation') || check_not_empty_doc($document, 'lon'))
    {
        echo '<li><ul itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        li(field_data_if_set($document, 'elevation', array('suffix' => 'meters', 'microdata' => 'elevation')));
        li(field_coord_data_if_set($document, 'lon', array('microdata' => 'longitude')));
        li(field_coord_data_if_set($document, 'lat', array('microdata' => 'latitude')));
        li(field_swiss_coords($document));
        echo '</ul></li>';
    }
    li(field_data_if_set($document, 'routes_quantity'));
    li(field_data_range_from_list_if_set($document, 'min_rating', 'max_rating', 'app_routes_rock_free_ratings', array('separator' => 'range separator')));
    li(field_data_from_list_if_set($document, 'mean_rating', 'app_routes_rock_free_ratings'));
    li(field_data_range_if_set($document, 'min_height', 'max_height', array('separator' => 'range separator', 'suffix' => 'meters')));
    li(field_data_if_set($document, 'mean_height', array('suffix' => 'meters')));
    li(field_data_from_list_if_set($document, 'equipment_rating', 'app_equipment_ratings_list'));
    li(field_data_from_list_if_set($document, 'climbing_styles', 'app_climbing_styles_list', array('multiple' => true)));
    li(field_data_from_list_if_set($document, 'rock_types', 'app_rock_types_list', array('multiple' => true)));
    li(field_data_from_list_if_set($document, 'children_proof', 'mod_sites_children_proof_list'));
    li(field_data_from_list_if_set($document, 'rain_proof', 'mod_sites_rain_proof_list'));
    if (count(array_diff(array(2, 4, 6, 8, 10, 12, 14, 16), $document->getRaw('facings'))) == 0)
    {
        li('<div class="section_subtitle" id="_facings">' . __('facings') . '</div> ' . __('all facings'));
    }
    else
    {
        li(field_data_from_list_if_set($document, 'facings', 'mod_sites_facings_list', array('multiple' => true)));
    }
    li(field_months_data($document, 'best_periods'));

    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')),
           array('class' => 'separator'));
    }
    ?>
</ul>
