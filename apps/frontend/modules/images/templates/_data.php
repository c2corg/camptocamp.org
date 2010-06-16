<?php
use_helper('Field', 'MyImage');

$image_type = $document['image_type'];
$licenses_array = sfConfig::get('app_licenses_list');
$license = $licenses_array[$image_type];
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix') . $sf_user->getCulture();
?>

    <ul id="article_gauche_5050" class="data">
        <?php
        disp_doc_type('image');
        if (!empty($user) && count($user))
        {
            $author = $document->get('author');
            if (!empty($author))
            {
                $uploaded_by_title = 'uploaded_by';
            }
            else
            {
                $uploaded_by_title = 'author';
            }
            li(_format_data($uploaded_by_title, link_to($user['name'], "@document_by_id?module=users&id=" . $user['id'])));
        }
        li(field_data_if_set($document, 'author'));
        ?>
        <?php li(field_data_from_list_if_set($document, 'image_type', 'mod_images_type_list', false)); ?>
        <li><div class="section_subtitle" id="_license"><?php echo __('Image license') ?></div>
        <a href="<?php echo $license_url ?>" rel="license" title="<?php echo __("$license title") ?>">Creative Commons <?php echo __($license) ?></a></li>
        <?php
        if ($document->get('has_svg'))
        {
            $svg_url = image_url($document->get('filename'), null, false, false, '.svg');
            echo li(_format_data('source file', absolute_link_to(__('svg file'), $svg_url)));
        }
        li(field_data_if_set($document, 'elevation', '', 'meters'));
        li(field_coord_data_if_set($document, 'lon'));
        li(field_coord_data_if_set($document, 'lat'));
        li(field_swiss_coords($document));
        li(field_activities_data_if_set($document));
        li(field_data_from_list_if_set($document, 'categories', 'mod_images_categories_list', true, false, '', '', '', 'image_categories'));
        
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
        li(field_data_if_set($document, 'id', '<input type="text" class="code" value="[img=',
                             ' right]'.$document->get('name').'[/img]"/>', 'topoguide_code'));
        li(field_data_if_set($document, 'filename', '<input type="text" class="code" value="[img=',
                             ' '.$sf_params->get('id').' inline]'.$document->get('name').'[/img]"/>', 'forum_code'));
        ?>
    </ul>
