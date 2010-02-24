<?php
/**
 * $Id: MyImageHelper.php 1662 2007-09-17 10:20:54Z fvanderbiest $
 */

use_helper('Link');

/**
 * $image : string
 */
function image_url($image, $type = null, $force_no_base = false, $use_temp = false, $new_ext = null)
{
    if(!is_null($type))
    {
        $images_types = sfConfig::get('app_images_types');
        $suffix = $images_types[$type]['suffix'];    
    }
    else
    {
        $suffix = '';
    }
    
    list($image_name, $image_ext) = Images::getFileNameParts($image);
     
    $base_path  = $force_no_base ? '' : sfConfig::get('app_static_url'); 
    $base_path .= DIRECTORY_SEPARATOR .
                  sfConfig::get('app_upload_dir') . DIRECTORY_SEPARATOR . 
                  ($use_temp ? sfConfig::get('app_images_temp_directory_name') : sfConfig::get('app_images_directory_name')) . DIRECTORY_SEPARATOR;
                 
    return $base_path . $image_name . $suffix . (isset($new_ext) ? $new_ext : $image_ext);
}

function display_picture($filename, $image_type = null, $size = 'big', $target_size = NULL, $title = 'Click to display original image')
{
    $image_url = image_url($filename, $size);
    $license_attr = null;
    if (isset($image_type))
    {
        $licenses = sfConfig::get('app_licenses_list');
        $license_url = sfConfig::get('app_licenses_base_url') . $licenses[$image_type] .
                       sfConfig::get('app_licenses_url_suffix') . sfContext::getInstance()->getUser()->getCulture();
        $license_attr = array('rel' => 'license', 'href' => $license_url);
    }
    $target_image_url = image_url($filename, $target_size, true);
    $absolute_url = absolute_link($target_image_url, true);

    return '<div class="picture"><a title="' . __($title) . '" href="' . $absolute_url . '">' . 
           image_tag($image_url, $license_attr) . '</a></div><div class="picture_right"></div>';
}
