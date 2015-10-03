<?php
use_helper('Field');
// put here meta tags for microdata which would be invalid inside ul tag
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=xreports&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}
?>
<ul id="article_gauche_5050" class="data">
    <?php
    li(field_activities_data($document));
    li(field_data_if_set($document, 'nb_participants'));
    li(field_data_if_set($document, 'nb_impacted'));
    li(field_data_from_list($document, 'event_type', 'mod_xreports_event_type_list', array('multiple' => true)));
    li(field_data_from_list($document, 'severity', 'mod_xreports_severity_list'));
    li(field_bool_data($document, 'rescue', array('null_equals_no' => true)));
    
    if (check_not_empty_doc($document, 'elevation') || check_not_empty_doc($document, 'lon'))
    {
        echo '<li><ul itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        li(field_data_if_set($document, 'elevation', array('suffix' => 'meters', 'microdata' => 'elevation')));
        li(field_coord_data_if_set($document, 'lon', array('microdata' => 'longitude')));
        li(field_coord_data_if_set($document, 'lat', array('microdata' => 'latitude')));
        li(field_swiss_coords($document));
        echo '</ul></li>';
    }

    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')),
           array('class' => 'separator'));
    }
    ?>
</ul>
