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
    public static function generateThumbnail($name, $ext, $path, $dimensions, $suffix, $square = false, $inflate = false, $strip = false, $progressive = false)
    {
        $path = $path . DIRECTORY_SEPARATOR;

        $adapter = (sfConfig::get('app_images_tool') === 'imagemagick') ? 'sfImageMagickAdapter' : 'sfGDAdapter';
 
        // delete thumbnail if already exists
        self::remove($path . $name . $suffix . $ext);

        // we pass maximum height and width dimensions, 
        // say that we preserve aspect ratio (scale=true), and that we do not want to inflate (inflate=false), and quality = 85%
        $thumbnail = new sfThumbnail($dimensions['width'], $dimensions['height'], true, $inflate, $square, 85, $adapter,
                                     array('keep_source_enable' => true, 'strip' => $strip, 'progressive' => $progressive));

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
            $strip = $type['strip'];
            $progressive = $type['progressive'];

            self::generateThumbnail($name, $ext, $path, $dimensions, $suffix, $square, $inflate, $strip, $progressive);
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

        // move svg if any
        if (file_exists($pathFrom . DIRECTORY_SEPARATOR . $file_name . '.svg'))
        {
            $success = $success && rename($pathFrom . DIRECTORY_SEPARATOR . $file_name . '.svg',
                                          $pathTo . DIRECTORY_SEPARATOR . $file_name .  '.svg');
        }

        return $success;
    }

    /**
     * Check if a svg file with same unique filename exists
     */
    public static function hasSVG($file, $path)
    {
        list($file_name, $file_ext) = self::getFileNameParts($file);

        return file_exists($path . DIRECTORY_SEPARATOR . $file_name . '.svg');
    }

    /**
     * Duplicate bug: different image docs may have the same file..
     */
    public static function isDuplicateFile($file)
    {
        return Doctrine_Query::create()
                   ->select('COUNT(i.id) num')
                   ->from('Image i')
                   ->where('i.filename = ?', $file)
                   ->execute()
                   ->getFirst()->num > 1;
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

        // unlink svg if any
        if (file_exists($path . $file_name . '.svg'))
        {
            $success = $success && self::remove($path . $file_name . '.svg');
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

    /*
     * Convert a png image to jpg image (used when cannot directly rasterising an svg as jpg) using php-gd or image magick
     * rq: default quality is 90 since the function is mostly used for schemas over photos, and we need a good visual aspect on edges
     */
    public static function png2jpg($unique_filename, $path, $quality=90)
    {
        if (sfConfig::get('app_images_tool') === 'imagemagick') { // use image magick
            exec('convert', $stdout);
            if (strpos($stdout[0], 'ImageMagick') === false)
            {
                throw new Exception(sprintf("ImageMagick convert command not found"));
            }

            $command .= ' -quality '.$quality.'% ';
            exec('convert '.$command.' '.escapeshellarg("$path$unique_filename.png").' '.escapeshellarg("$path$unique_filename.jpg"));
            unlink("$path$unique_filename.png");
        }
        else // use php-gd
        {
            if (!extension_loaded('gd'))
            {
                throw new Exception ('GD not enabled. Check your php.ini file.');
            }

            $size = getimagesize("$path$unique_filename.png");
            $bg_image = imagecreatetruecolor($size[0],$size[1]);
            $mycolor = imagecolorallocate($bg_image, 255, 255, 255);
            imagefill($bg_image, 0, 0, $mycolor);
            $image = imagecreatefrompng("$path$unique_filename.png");
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagecopy($bg_image, $image, 0, 0, 0, 0, $size[0], $size[1]);
            imagejpeg($bg_image, "$path$unique_filename.jpg", $quality);
            imagedestroy($image);
            imagedestroy($bg_image);
            unlink("$path$unique_filename.png");
        }
    }

    /*
     * Automagically orient (rotate) an image created by a digital camera
     * For now, only effective if imagemagick is used
     */
    public static function correctOrientation($file)
    {
        $exif = exif_read_data($file, 'IFD0');
        if ($exif && isset($exif['Orientation']) && $exif['Orientation'] != 1
                && sfConfig::get('app_images_tool') === 'imagemagick') {

            exec('convert', $stdout);
            if (strpos($stdout[0], 'ImageMagick') === false)
            {
                throw new Exception('ImageMagick convert command not found');
            }

            exec('convert '.escapeshellarg("$file").' -auto-orient '.escapeshellarg("$file"));
        }

        // else, we do nothing...
    }

    /*
     * Rotate an image by 90 or -90 degrees
     */
    public static function rotateImage($input_file, $output_file, $degrees = 90)
    {
        $degrees = ($degrees === 90) ? $degrees : -90;

        if (!file_exists($input_file))
        {
            throw new Exception('Cannot rotate non-existing file!');
        }

        if (sfConfig::get('app_images_tool') === 'imagemagick') { // use image magick
            exec('convert', $stdout);
            if (strpos($stdout[0], 'ImageMagick') === false)
            {
                throw new Exception('ImageMagick convert command not found');
            }

            exec('convert '.escapeshellarg("$input_file").' -rotate '.$degrees.' '.escapeshellarg("$output_file"));

        }
        else // use php-gd
        {
            if (!extension_loaded('gd'))
            {
                throw new Exception('GD not enabled. Check your php.ini file');
            }

            // TODO
            throw new Exception('GD based image rotation not yet implemented in c2c');
        }
    }
}
