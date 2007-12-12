<?php
/**
 * $Id: MyImageHelper.php 1662 2007-09-17 10:20:54Z fvanderbiest $
 */


/**
 * $image : string
 */
function image_url($image, $type = null)
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
     
    $base_path = DIRECTORY_SEPARATOR .
                 sfConfig::get('app_upload_dir') . DIRECTORY_SEPARATOR . 
                 sfConfig::get('app_images_directory_name') . DIRECTORY_SEPARATOR;
                 
    return $base_path . $image_name . $suffix . $image_ext;
}


function display_picture($filename, $size = 'big', $target_size = NULL, $title = 'Click to display original image')
{
    $image_url = image_url($filename, $size);
    $target_image_url = image_url($filename, $target_size);
    return '<div class="picture"><a title="' . __($title) . '" href="http://' . $_SERVER["SERVER_NAME"] . $target_image_url . '">' . image_tag($image_url) . '</a></div><div class="picture_right"></div>';
}