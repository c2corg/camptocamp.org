<?php
use_helper('Field', 'MyImage');

$mobile_version = c2cTools::mobileVersion();
$image_type = $document['image_type'];
$licenses_array = sfConfig::get('app_licenses_list');
$license = $licenses_array[$image_type];
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix') . $sf_user->getCulture();

// put here meta tags for microdata which would be invalid inside ul tag
echo microdata_meta('name', $document->getName());
if (isset($nb_comments) && $nb_comments)
{
    echo microdata_meta('interactionCount', $nb_comments . ' UserComments');
    echo microdata_meta('discussionUrl', url_for('@document_comment?module=images&id='.$sf_params->get('id').'&lang='.$sf_params->get('lang')));
}?>
<ul class="data col_left col_66">
    <?php
    if (!empty($user) && count($user))
    {
        $author = $document->get('author');
        if (!empty($author) || $image_type == 3)
        {
            $uploaded_by_title = 'uploaded_by';
            $options = null;
        }
        else
        {
            $uploaded_by_title = 'author';
            $options = array('itemprop' => 'author');
        }
        li(_format_data($uploaded_by_title, link_to($user['name'], "@document_by_id?module=users&id=" . $user['id'], $options)));
    }
    li(field_data_if_set($document, 'author', array('microdata' => 'author')));
    li(field_data_from_list_if_set($document, 'image_type', 'mod_images_type_full_list'));

    if ($image_type != 3) // 3 = copyright
    {
    ?>
         <li><div class="section_subtitle" id="_license"><?php echo __('Image license') ?></div>
         <?php
         $license_link_opt = array('title' => __("$license title"),
                                   'rel' => 'license');
         if (!$mobile_version) $license_link_opt['about'] = image_url($document->get('filename'), null);
         echo link_to('Creative Commons '.__($license), $license_url, $license_link_opt).'</li>';
    }

    li(field_image_details($document));
    li(field_data_if_set($document, 'date_time', array('microdata' => 
       array('tag' => 'time', 'itemprop' => 'dateCreated', 'datetime' => str_replace(' ', 'T', $document->getDateTime())))),
       array('class' => 'separator'));

    if (check_not_empty_doc($document, 'elevation') || check_not_empty_doc($document, 'lon'))
    {
        echo '<li itemprop="contentLocation" itemscope itemtype="http://schema.org/Place">',
             '<ul itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">';
        li(field_data_if_set($document, 'elevation', array('suffix' => 'meters', 'microdata' => 'elevation')));
        li(field_coord_data_if_set($document, 'lon', array('microdata' => 'longitude')));
        li(field_coord_data_if_set($document, 'lat', array('microdata' => 'latitude')));
        li(field_swiss_coords($document));
        echo '</ul></li>';
    }

    li(field_activities_data_if_set($document));
    li(field_data_from_list_if_set($document, 'categories', 'mod_images_categories_list', array('multiple' => true, 'title_id' => 'image_categories')));

    li(field_data_if_set($document, 'camera_name'), array('class' => 'separator'));
    li(field_data_if_set($document, 'focal_length', array('suffix' => 'mm')));
    li(field_data_if_set($document, 'fnumber', array('prefix' => 'F/')));
    li(field_exposure_time_if_set($document));
    li(field_data_if_set($document, 'iso_speed', array('suffix' => ' ISO')));

    if (!$mobile_version): 
    li(field_data_if_set($document, 'id', array('prefix' => '<input type="text" class="code" value="[img=',
                         'suffix' => ' right]'.$document->get('name').'[/img]"/>', 'title' => 'topoguide_code')), array('class' => 'separator'));
    li(field_data_if_set($document, 'filename', array('prefix' => '<input type="text" class="code" value="[img=',
                         'suffix' => ' '.$sf_params->get('id').' inline]'.$document->get('name').'[/img]"/>',
                         'title' => 'forum_code')));
    endif;
    
    if ($document->get('has_svg'))
    {
        $svg_url = image_url($document->get('filename'), null, false, false, '.svg');
        li(_format_data('source file', content_tag('a', __('svg file'), array('href' => $svg_url))));
    }
    if ($document->get('geom_wkt'))
    {
        li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang'), $sf_params->get('version')));
    }
    ?>
</ul>
