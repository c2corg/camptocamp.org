<?php
use_helper('Field');
// put here meta tags for microdata which would be invalid inside ul tag
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=huts&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul id="article_gauche_5050" class="data">
    <?php
    li(field_data_from_list($document, 'shelter_type', 'mod_huts_shelter_types_list'));

    if (check_not_empty_doc($document, 'elevation') || check_not_empty_doc($document, 'lon'))
    {
        echo '<li><ul itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        li(field_data_if_set($document, 'elevation', array('suffix' => 'meters', 'microdata' => 'elevation')));
        li(field_coord_data_if_set($document, 'lon', array('microdata' => 'longitude')));
        li(field_coord_data_if_set($document, 'lat', array('microdata' => 'latitude')));
        li(field_swiss_coords($document));
        echo '</ul></li>';
    }
    li(field_bool_data($document, 'is_staffed', array('null_equals_no' => true)));
    li(field_data_if_set($document, 'staffed_capacity'));
    li(field_data_if_set($document, 'unstaffed_capacity'));
    
    
   
    // don't show only if not applicable 
    if ($document->get('has_unstaffed_matress') <> 10 ){ 
        li(field_data_from_list($document, 'has_unstaffed_matress', 'app_boolean_list'));
    }
    if ($document->get('has_unstaffed_blanket') <> 10 ){
        li(field_data_from_list($document, 'has_unstaffed_blanket', 'app_boolean_list'));
    }
    if ($document->get('has_unstaffed_gas') <> 10 ){
        li(field_data_from_list($document, 'has_unstaffed_gas', 'app_boolean_list'));
    }
    if ($document->get('has_unstaffed_wood') <> 10 ){
        li(field_data_from_list($document, 'has_unstaffed_wood', 'app_boolean_list'));
    }
    
    li(field_phone_if_set($document, 'phone', array('microdata' => 'telephone')));
    li(field_url_data_if_set($document, 'url', array('microdata' => 'url')));
    li(field_activities_data($document)); 
    
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')), array('class' => 'separator'));
    }
    ?>
</ul>
