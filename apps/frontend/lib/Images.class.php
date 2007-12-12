<?php
/**
 * Class Images
 * $Id: images.php,v 1.26 2003/02/11 15:13:55 asaunier Exp $
 */
class Images
{
    /**
     * Separate core filename and extension from a filename.
     * @param string filename
     * @return array
     **/
    public static function getFileNameParts($filename)
    {
        $temp = explode('.', $filename);
        $extension = '.' . array_pop($temp);
        $name = array_pop($temp);
        return array($name, $extension);
    }

    /**
     * Get file extension from a file path (eg. /tmp/photo.jpg) using file type info.
     *
     * @input string filepath
     * @return string ext
     **/
    public static function detectExtension($filepath)
    {
        return '.' . c2cTools::getFileType($filepath);
    }

    /**
     * Resizes an image.
     * if $square = true, make resulting image square
     */
    public static function generateThumbnail($name, $ext, $path, $dimensions, $suffix, $square = false, $inflate = false)
    {
        $path = $path . DIRECTORY_SEPARATOR;
         
        // delete thumbnail if already exists
        self::remove($path . $name . $suffix . $ext);

        // we pass maximum height and width dimensions, 
        // say that we preserve aspect ratio (scale=true), and that we do not want to inflate (inflate=false), and quality = 85 %
        $thumbnail = new sfThumbnail($dimensions['height'], $dimensions['width'], true, $inflate, $square, 85, 'sfGDAdapter');

        $thumbnail->loadFile($path . $name . $ext);
        $thumbnail->save($path . $name . $suffix . $ext);
    }

    /**
     * Generates resized versions of uploaded images.
     */
    public static function generateThumbnails($name, $ext, $path)
    {
        $types = sfConfig::get('app_images_types');

        foreach ($types as $type)
        {
            $suffix = $type['suffix'];
            $dimensions = $type['dimensions'];
            $square = $type['square'];
            $inflate = $type['inflate'];

            self::generateThumbnail($name, $ext, $path, $dimensions, $suffix, $square, $inflate);
        }
    }
    
    /**
     * Move given image and its resized versions.
     */
    public static function moveAll($file, $pathFrom, $pathTo)
    {
        c2cTools::log("moving $file from $pathFrom to $pathTo");

        $types = sfConfig::get('app_images_types', array());
        list($file_name, $file_ext) = self::getFileNameParts($file);

        // move original
        $success = rename($pathFrom . DIRECTORY_SEPARATOR . $file, $pathTo . DIRECTORY_SEPARATOR . $file);

        // move thumbs
        foreach($types as $type)
        {
            $success = $success && rename($pathFrom . DIRECTORY_SEPARATOR . $file_name . $type['suffix'] . $file_ext,
                                          $pathTo . DIRECTORY_SEPARATOR . $file_name . $type['suffix'] . $file_ext);
        }

        return $success;
    }

    /**
     * Remove given image and its resized versions.
     */
    public static function removeAll($file, $path)
    {
        $types = sfConfig::get('app_images_types', array());
        $path .= DIRECTORY_SEPARATOR;
        
        list($file_name, $file_ext) = self::getFileNameParts($file);

        // unlink original
        $success = self::remove($path . $file);

        // unlink thumbs
        foreach($types as $type)
        {
            $success = $success && self::remove($path . $file_name . $type['suffix'] . $file_ext);
        }

        return $success;
    }

    /**
     * Delete a file if it exists.
     */
    public static function remove($file_with_path)
    {
        if (file_exists($file_with_path))
        {
            return unlink($file_with_path);
        }
        return false;
    }
    
}
