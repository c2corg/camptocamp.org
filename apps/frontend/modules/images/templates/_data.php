<?php
use_helper('Field');

$image_type = $document['image_type'];
$licenses_array = sfConfig::get('app_licenses_list');
$license = $licenses_array[$image_type];
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix') . $sf_user->getCulture();
?>

<div class="article_contenu">
    <ul class="data">
        <?php
        disp_doc_type('image');
        if (!empty($user) && count($user))
        {
            $author = $document->get('author');
            if (!empty($author))
            {
                $uploade_by_title = 'uploaded_by';
            }
            else
            {
                $uploade_by_title = 'author';
            }
            li(_format_data($uploade_by_title, link_to($user['name'], "@document_by_id?module=users&id=" . $user['id'])));
        }
        li(field_data_if_set($document, 'author'));
        ?>
        <?php li(field_data_from_list_if_set($document, 'image_type', 'mod_images_type_list', false)); ?>
        <li><div class="section_subtitle" id="_license"><?php echo __('Image license') ?></div>
        <a href="<?php echo $license_url ?>" rel="license" title="<?php echo __("$license title") ?>">Creative Commons <?php echo __($license) ?></a></li>
        <?php
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_activities_data_if_set($document));
        li(field_data_from_list_if_set($document, 'categories', 'mod_images_categories_list', true));
        
        if ($document->get('geom_wkt'))
        {
            li(field_export($document->get('module'), $sf_params->get('id'), $sf_params->get('lang')));
        }

        li(field_data_if_set($document, 'camera_name'));
        li(field_data_if_set($document, 'date_time'));
        li(field_data_if_set($document, 'focal_length', '', 'mm'));
        li(field_data_if_set($document, 'fnumber', 'F/'));
        li(field_exposure_time_if_set($document));
        li(field_data_if_set($document, 'iso_speed', '', ' ISO'));
        li(field_data($document, 'filename', '<input type="text" value="[img=', '|right]'.$document->get('name').'[/img]"/>'));
        li(field_data($document, 'filename', '<input type="text" value="[url=/images/'.$sf_params->get('id').'][img=', '|inline]'.$document->get('name').'[/img][/url]"/>', 'forum_code'));
        ?>
    </ul>
</div>
