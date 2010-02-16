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
        $thumbnail = new sfThumbnail($dimensions['width'], $dimensions['height'], true, $inflate, $square, 88, 'sfGDAdapter', array('keep_source_enable' => true));

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

    /**
     * Create the rasterized version of a SVG file
     * FIXME many things to be improved
     */
    public static function rasterizeSVG($path, $unique_filename, &$file_ext)
    {
        $svg_rasterizer = sfConfig::get('app_images_svg_rasterizer');

        // determine whether we should output a PNG or a JPG image FIXME the methods we use is os-dependant
        // and most certainly quite dumb, but no better idea yet
        $png = (intval(exec("grep '<image ' $path$unique_filename.svg  | wc -l")) == 0);

        // FIXME depending on the output format, we determine the max-width
        // probably we should do something better
        $width = 3000;

        switch ($svg_rasterizer)
        {
            case 'batik': // TODO Seems to have problems with jpg output
                $cmd = 'extra_args="-Djava.awt.headless=true" rasterizer -bg 255.255.255.255 -m '.($png ? 'image/png' : 'image/jpg').
                       " -w $width -d $path$unique_filename.".($png ? 'png' : 'jpg')." $path$unique_filename.svg";
                break;
            case 'rsvg': // TODO Does not supports jpeg anymore, so we would have to perform  second conversion
                $cmd = "rsvg -w$width -f ".($png ? 'png' : 'jpeg').
                       " $path$unique_filename.svg $path$unique_filename.".($png ? 'png' : 'jpg');
                break;
            case 'convert':
                $cmd = "convert -background white -resize $width"."x$width ".$path.$unique_filename.'.svg '.
                       ($png ? 'PNG:' : 'JPG:').$path.$unique_filename.($png ? '.png' : '.jpg');
                break;
        }
        exec($cmd);

        $file_ext = $png ? '.png' : '.jpg';

        // check that file truly exists to determine if rasterization went ok FIXME probably not the best way...
        return file_exists($path.$unique_filename.$file_ext);
    }
}
