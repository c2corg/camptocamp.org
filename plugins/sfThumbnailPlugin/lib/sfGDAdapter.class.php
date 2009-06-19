<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfGDAdapter provides a mechanism for creating thumbnail images.
 * @see http://www.php.net/gd
 *
 * @package    sfThumbnailPlugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Benjamin Meynell <bmeynell@colorado.edu>
 */

class sfGDAdapter
{

  protected
    $sourceName,
    $sourceWidth,
    $sourceHeight,
    $sourceMime,
    $maxWidth,
    $maxHeight,
    $scale,
    $square,
    $inflate,
    $quality,
    $keep_source_enable,
    $keep_source,
    $source,
    $thumb;

  /**
   * List of accepted image types based on MIME
   * descriptions that this adapter supports
   */
  protected $imgTypes = array(
    'image/jpeg',
    'image/pjpeg',
    'image/png',
    'image/gif',
  );

  /**
   * Stores function names for each image type
   */
  protected $imgLoaders = array(
    'image/jpeg'  => 'imagecreatefromjpeg',
    'image/pjpeg' => 'imagecreatefromjpeg',
    'image/png'   => 'imagecreatefrompng',
    'image/gif'   => 'imagecreatefromgif',
  );

  /**
   * Stores function names for each image type
   */
  protected $imgCreators = array(
    'image/jpeg'  => 'imagejpeg',
    'image/pjpeg' => 'imagejpeg',
    'image/png'   => 'imagepng',
    'image/gif'   => 'imagegif',
  );

  public function __construct($maxWidth, $maxHeight, $scale, $inflate, $square, $quality, $options)
  {
    if (!extension_loaded('gd'))
    {
      throw new Exception ('GD not enabled. Check your php.ini file.');
    }
    $this->maxWidth = $maxWidth;
    $this->maxHeight = $maxHeight;
    $this->scale = $scale;
    $this->inflate = $inflate;
    $this->square = $square;
    $this->quality = $quality;
    $this->options = $options;
    $this->keep_source_enable = isset($options['keep_source_enable']) ? $options['keep_source_enable'] : false;
  }

  public function loadFile($thumbnail, $image)
  {
    $this->sourceName = $image;
    $imgData = @GetImageSize($image);

    if (!$imgData)
    {
      throw new Exception(sprintf('Could not load image %s', $image));
    }

    if (in_array($imgData['mime'], $this->imgTypes))
    {
      $loader = $this->imgLoaders[$imgData['mime']];
      if(!function_exists($loader))
      {
        throw new Exception(sprintf('Function %s not available. Please enable the GD extension.', $loader));
      }

      $this->source = $loader($image);
      $this->sourceWidth = $imgData[0];
      $this->sourceHeight = $imgData[1];
      $this->sourceMime = $imgData['mime'];
      $thumbnail->initThumb($this->sourceWidth, $this->sourceHeight, $this->maxWidth, $this->maxHeight, $this->scale, $this->inflate, $this->square);

      if (($imgData[0] == $this->maxWidth && $imgData[1] == $this->maxHeight) || (!$this->inflate && $imgData[0] <= $this->maxWidth && $imgData[1] <= $this->maxHeight))
      {
        $this->thumb = $this->source;
        $this->keep_source = $this->keep_source_enable;
      }
      else
      {
          $this->thumb = imagecreatetruecolor($thumbnail->getThumbWidth(), $thumbnail->getThumbHeight());
          $this->keep_source = false;
          
          if ($this->square)
          {
            $min_orig = min($imgData[0], $imgData[1]);
            imagecopyresampled($this->thumb, $this->source, 0, 0, ($imgData[0] - $min_orig) / 2, ($imgData[1] - $min_orig) / 2, $thumbnail->getThumbWidth(), $thumbnail->getThumbHeight(), $min_orig, $min_orig);
          }
          else
          {
            imagecopyresampled($this->thumb, $this->source, 0, 0, 0, 0, $thumbnail->getThumbWidth(), $thumbnail->getThumbHeight(), $imgData[0], $imgData[1]);
          }
      }

      return true;
    }
    else
    {
      throw new Exception(sprintf('Image MIME type %s not supported', $imgData['mime']));
    }
  }

  public function loadData($thumbnail, $image, $mime)
  {
    if (in_array($mime,$this->imgTypes))
    {
      $this->source = imagecreatefromstring($image);
      $this->sourceWidth = imagesx($this->source);
      $this->sourceHeight = imagesy($this->source);
      $this->sourceMime = $mime;
      $thumbnail->initThumb($this->sourceWidth, $this->sourceHeight, $this->maxWidth, $this->maxHeight, $this->scale, $this->inflate, $this->square);

      $this->thumb = imagecreatetruecolor($thumbnail->getThumbWidth(), $thumbnail->getThumbHeight());
      $this->keep_source = false;
      if (($this->sourceWidth == $this->maxWidth && $this->sourceHeight == $this->maxHeight) || (!$this->inflate && $this->sourceWidth <= $this->maxWidth && $this->sourceHeight <= $this->maxHeight))
      {
        $this->thumb = $this->source;
        $this->keep_source = $this->keep_source_enable;
      }
      else
      {
        imagecopyresampled($this->thumb, $this->source, 0, 0, 0, 0, $thumbnail->getThumbWidth(), $thumbnail->getThumbHeight(), $this->sourceWidth, $this->sourceHeight);
      }

      return true;
    }
    else
    {
      throw new Exception(sprintf('Image MIME type %s not supported', $mime));
    }
  }

  public function save($thumbnail, $thumbDest, $targetMime = null)
  {
    if($this->keep_source)
    {
      link($this->sourceName, $thumbDest);
    }
    else
    {
      $sharpen = array(array(-1, -1, -1), array(-1, 28, -1), array(-1, -1, -1));
    //  imageconvolution($this->thumb, $sharpen, 20, 0);
      
      if($targetMime !== null)
      {
        $creator = $this->imgCreators[$targetMime];
      }
      else
      {
        $creator = $this->imgCreators[$thumbnail->getMime()];
      }

      if ($creator == 'imagejpeg')
      {
        imagejpeg($this->thumb, $thumbDest, $this->quality);
      }
      else
      {
        $creator($this->thumb, $thumbDest);
      }
    }
  }

  public function freeSource()
  {
    if (is_resource($this->source))
    {
      imagedestroy($this->source);
    }
  }

  public function freeThumb()
  {
    if (is_resource($this->thumb))
    {
      imagedestroy($this->thumb);
    }
  }

  public function getSourceMime()
  {
    return $this->sourceMime;
  }

}
