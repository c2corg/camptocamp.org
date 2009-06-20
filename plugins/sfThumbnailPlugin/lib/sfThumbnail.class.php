<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfThumbnail provides a mechanism for creating thumbnail images.
 *
 * This is taken from Harry Fueck's Thumbnail class and 
 * converted for PHP5 strict compliance for use with symfony.
 *
 * @package    sfThumbnailPlugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Benjamin Meynell <bmeynell@colorado.edu>
 */
class sfThumbnail
{
  /**
   * Width of thumbnail in pixels
   */
  protected $thumbWidth;

  /**
   * Height of thumbnail in pixels
   */
  protected $thumbHeight;

  /**
   * Thumbnail constructor
   *
   * @param int (optional) max width of thumbnail
   * @param int (optional) max height of thumbnail
   * @param boolean (optional) if true image scales
   * @param boolean (optional) if true inflate small images
   * @param string (optional) adapter class name
   * @param array (optional) adapter options
   */
  public function __construct($maxWidth = null, $maxHeight = null, $scale = true, $inflate = true, $square = false, $quality = 75, $adapterClass = null, $adapterOptions = array())
  {
    if (!$adapterClass)
    {
      if (extension_loaded('gd'))
      {
        $adapterClass = 'sfGDAdapter';
      }
      else
      {
        $adapterClass = 'sfImageMagickAdapter';
      }
    }
    $this->adapter = new $adapterClass($maxWidth, $maxHeight, $scale, $inflate, $square, $quality, $adapterOptions);
  }

  /**
   * Loads an image from a file and creates an internal thumbnail out of it
   *
   * @param string filename (with absolute path) of the image to load
   *
   * @return boolean True if the image was properly loaded
   * @throws Exception If the image cannot be loaded, or if its mime type is not supported
   */
  public function loadFile($image)
  {
    $this->adapter->loadFile($this, $image);
  }

  /**
  * Loads an image from a string (e.g. database) and creates an internal thumbnail out of it
  *
  * @param string the image string (must be a format accepted by imagecreatefromstring())
  * @param string mime type of the image
  *
  * @return boolean True if the image was properly loaded
  * @access public
  * @throws Exception If image mime type is not supported
  */
  public function loadData($image, $mime)
  {
    $this->adapter->loadData($this, $image, $mime);
  }

  /**
   * Saves the thumbnail to the filesystem
   * If no target mime type is specified, the thumbnail is created with the same mime type as the source file.
   *
   * @param string the image thumbnail file destination (with absolute path)
   * @param string The mime-type of the thumbnail (possible values are 'image/jpeg', 'image/png', and 'image/gif')
   *
   * @access public
   * @return void
   */
  public function save($thumbDest, $targetMime = null)
  {
    $this->adapter->save($this, $thumbDest, $targetMime);
  }

  public function freeSource()
  {
    $this->adapter->freeSource();
  }

  public function freeThumb()
  {
    $this->adapter->freeThumb();
  }

  public function freeAll()
  {
    $this->adapter->freeSource();
    $this->adapter->freeThumb();
  }

  /**
   * Returns the width of the thumbnail
   */
  public function getThumbWidth()
  {
    return $this->thumbWidth;
  }

  /**
   * Returns the height of the thumbnail
   */
  public function getThumbHeight()
  {
    return $this->thumbHeight;
  }

  /**
   * Returns the mime type of the source image
   */
  public function getMime()
  {
    return $this->adapter->getSourceMime();
  }

  /**
   * Computes the thumbnail width and height
   * Used by adapter
   */
  public function initThumb($sourceWidth, $sourceHeight, $maxWidth, $maxHeight, $scale, $inflate, $square)
  {
    if ($maxWidth > 0)
    {
      $ratioWidth = $maxWidth / $sourceWidth;
    }
    if ($maxHeight > 0)
    {
      $ratioHeight = $maxHeight / $sourceHeight;
    }

    if ($scale)
    {
      // aspect ratio is preserved
      if ($maxWidth && $maxHeight)
      {
        if ($square)
        {
          $ratio = ($ratioWidth < $ratioHeight) ? $ratioHeight : $ratioWidth;
        }
        else
        {
          $ratio = ($ratioWidth < $ratioHeight) ? $ratioWidth : $ratioHeight;
        }
      }
      if ($maxWidth xor $maxHeight)
      {
        $ratio = (isset($ratioWidth)) ? $ratioWidth : $ratioHeight;
      }
      if ((!$maxWidth && !$maxHeight) || (!$inflate && $ratio > 1))
      {
        $ratio = 1;
      }

      $this->thumbWidth = floor($ratio * $sourceWidth);
      $this->thumbHeight = ceil($ratio * $sourceHeight);
      if ($square)
      {
        $this->thumbWidth = min($this->thumbWidth, $maxWidth);
        $this->thumbHeight = min($this->thumbHeight, $maxHeight);
      }
    }
    else
    {
      // aspect ratio is not preserved
      if (!isset($ratioWidth) || (!$inflate && $ratioWidth > 1))
      {
        $ratioWidth = 1;
      }
      if (!isset($ratioHeight) || (!$inflate && $ratioHeight > 1))
      {
        $ratioHeight = 1;
      }
      $this->thumbWidth = floor($ratioWidth * $sourceWidth);
      $this->thumbHeight = ceil($ratioHeight * $sourceHeight);
    }
  }

  public function __destruct()
  {
    $this->freeAll();
  }
}
