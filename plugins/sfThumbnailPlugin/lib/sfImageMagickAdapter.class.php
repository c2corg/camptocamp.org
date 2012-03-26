<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfImageMagickAdapter provides a mechanism for creating thumbnail images.
 * @see http://www.imagemagick.org
 *
 * @package    sfThumbnailPlugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Benjamin Meynell <bmeynell@colorado.edu>
 */

class sfImageMagickAdapter
{

  protected
    $sourceName,
    $sourceWidth,
    $sourceHeight,
    $sourceMime,
    $maxWidth,
    $maxHeight,
    $scale,
    $inflate,
    $square,
    $quality,
    $keep_source_enable,
    $keep_source,
    $source,
    $strip, // whether we should strip the image of any profile or comments (exif, iptc, etc)
    $progressive, // whether we should use baseline or progressive jpegs (see http://www.yuiblog.com/blog/2008/12/05/imageopt-4/)
    $magickCommands;

  /**
   * Mime types this adapter supports
   */
  protected $imgTypes = array(
    'application/pdf',
    'application/postscript',
    'application/vnd.palm',
    'application/x-icb',
    'application/x-mif',
    'image/dcx',
    'image/g3fax',
    'image/gif',
    'image/jng',
    'image/jpeg',
    'image/pbm',
    'image/pcd',
    'image/pict',
    'image/pjpeg',
    'image/png',
    'image/ras',
    'image/sgi',
    'image/svg',
    'image/tga',
    'image/tiff',
    'image/vda',
    'image/vnd.wap.wbmp',
    'image/vst',
    'image/x-fits',
    'image/x-ms-bmp',
    'image/x-otb',
    'image/x-palm',
    'image/x-pcx',
    'image/x-pgm',
    'image/x-photoshop',
    'image/x-ppm',
    'image/x-ptiff',
    'image/x-viff',
    'image/x-win-bitmap',
    'image/x-xbitmap',
    'image/x-xv',
    'image/xpm',
    'image/xwd',
    'text/plain',
    'video/mng',
    'video/mpeg',
    'video/mpeg2',
  );

  /**
   * Imagemagick-specific Type to Mime type map
   */
  protected $mimeMap = array(
    'bmp'   => 'image/bmp',
    'bmp2'  => 'image/bmp',
    'bmp3'  => 'image/bmp',
    'cur'   => 'image/x-win-bitmap',
    'dcx'   => 'image/dcx',
    'epdf'  => 'application/pdf',
    'epi'   => 'application/postscript',
    'eps'   => 'application/postscript',
    'eps2'  => 'application/postscript',
    'eps3'  => 'application/postscript',
    'epsf'  => 'application/postscript',
    'epsi'  => 'application/postscript',
    'ept'   => 'application/postscript',
    'ept2'  => 'application/postscript',
    'ept3'  => 'application/postscript',
    'fax'   => 'image/g3fax',
    'fits'  => 'image/x-fits',
    'g3'    => 'image/g3fax',
    'gif'   => 'image/gif',
    'gif87' => 'image/gif',
    'icb'   => 'application/x-icb',
    'ico'   => 'image/x-win-bitmap',
    'icon'  => 'image/x-win-bitmap',
    'jng'   => 'image/jng',
    'jpeg'  => 'image/jpeg',
    'jpg'   => 'image/jpeg',
    'm2v'   => 'video/mpeg2',
    'miff'  => 'application/x-mif',
    'mng'   => 'video/mng',
    'mpeg'  => 'video/mpeg',
    'mpg'   => 'video/mpeg',
    'otb'   => 'image/x-otb',
    'p7'    => 'image/x-xv',
    'palm'  => 'image/x-palm',
    'pbm'   => 'image/pbm',
    'pcd'   => 'image/pcd',
    'pcds'  => 'image/pcd',
    'pcl'   => 'application/pcl',
    'pct'   => 'image/pict',
    'pcx'   => 'image/x-pcx',
    'pdb'   => 'application/vnd.palm',
    'pdf'   => 'application/pdf',
    'pgm'   => 'image/x-pgm',
    'picon' => 'image/xpm',
    'pict'  => 'image/pict',
    'pjpeg' => 'image/pjpeg',
    'png'   => 'image/png',
    'png24' => 'image/png',
    'png32' => 'image/png',
  );

  public function __construct($maxWidth, $maxHeight, $scale, $inflate, $square, $quality, $options)
  {
    $this->magickCommands = array();
    $this->magickCommands['convert'] = isset($options['convert']) ? escapeshellcmd($options['convert']) : 'convert';
    $this->magickCommands['identify'] = isset($options['identify']) ? escapeshellcmd($options['identify']) : 'identify';

    exec($this->magickCommands['convert'], $stdout);
    if (strpos($stdout[0], 'ImageMagick') === false)
    {
      throw new Exception(sprintf("ImageMagick convert command not found"));
    }

    exec($this->magickCommands['identify'], $stdout);
    if (strpos($stdout[0], 'ImageMagick') === false)
    {
      throw new Exception(sprintf("ImageMagick identify command not found"));
    }

    $this->maxWidth = $maxWidth;
    $this->maxHeight = $maxHeight;
    $this->scale = $scale;
    $this->inflate = $inflate;
    $this->square = $square;
    $this->quality = $quality;
    $this->options = $options;
    $this->keep_source_enable = isset($options['keep_source_enable']) ? $options['keep_source_enable'] : false;
    $this->strip = isset($options['strip']) ? $options['strip'] : false;
    $this->progressive = isset($options['progressive']) ? $options['progressive'] : false;
  }

  public function loadFile($thumbnail, $image)
  {
    $this->sourceName = $image;

    // try and use getimagesize()
    // on failure, use identify instead
    $imgData = @getimagesize($image);
    if (!$imgData)
    {
      exec($this->magickCommands['identify'].' '.escapeshellarg($image), $stdout, $retval);
      if ($retval === 1)
      {
        throw new Exception('Image could not be identified.');
      }
      else
      {
        // get image data via identify
        list($img, $type, $dimen) = explode(' ', $stdout[0]);
        list($width, $height) = explode('x', $dimen);

        $this->sourceWidth = $width;
        $this->sourceHeight = $height;
        $this->sourceMime = $this->mimeMap[strtolower($type)];
      }
    }
    else
    {
      // use image data from getimagesize()
      $this->sourceWidth = $imgData[0];
      $this->sourceHeight = $imgData[1];
      $this->sourceMime = $imgData['mime'];
    }
    $this->image = $image;

    // open file resource
    $source = fopen($image, 'r');

    $this->source = $source;

    $thumbnail->initThumb($this->sourceWidth, $this->sourceHeight, $this->maxWidth, $this->maxHeight, $this->scale, $this->inflate, $this->square);

    // if the image is smaller than thumb size, we only make a hard link
    if (($this->sourceWidth == $this->maxWidth && $this->sourceHeight == $this->maxHeight) ||
        (!$this->inflate && $this->sourceWidth <= $this->maxWidth && $this->sourceHeight <= $this->maxHeight))
    {
      $this->keep_source = $this->keep_source_enable;
    }

    return true;
  }

  public function loadData($thumbnail, $image, $mime)
  {
    throw new Exception('This function is not yet implemented. Try a different adapter.');
  }

  public function save($thumbnail, $thumbDest, $targetMime = null)
  {
    // if the image is smaller than thumb size, we only make a hard link
    if ($this->keep_source)
    {
      link($this->sourceName, $thumbDest);
      return;
    }

    $command = ' -thumbnail ';
    $tsize = $thumbnail->getThumbWidth().'x'.$thumbnail->getThumbHeight();

    $command .= $tsize;

    // see http://www.imagemagick.org/Usage/thumbnails/#cut and http://www.imagemagick.org/Usage/resize/#space_fill
    if ($this->square)
    {
      $command .= '^ -gravity center -compose copy -extent '.$tsize;
    }

    // absolute sizing rq: this is incompatible with square
    if (!$this->scale)
    {
      $command .= '!';
    }

    // whether we should remove profiles and comments from images
    if ($this->strip)
    {
      $command .= ' -strip';
    }

    // for jpegs >10K, progressive jpegs have better compression +
    // it is more user friendly
    // http://www.yuiblog.com/blog/2008/12/05/imageopt-4/
    if ($this->progressive && $thumbnail->getMime() == 'image/jpeg')
    {
        $command .= ' -interlace';
    }

    if ($this->quality && $thumbnail->getMime() == 'image/jpeg')
    {
      $command .= ' -quality '.$this->quality.'% ';
    }

    // extract images such as pages from a pdf doc
    $extract = '';
    if (isset($this->options['extract']) && is_int($this->options['extract']))
    {
      if ($this->options['extract'] > 0)
      {
        $this->options['extract']--;
      }
      $extract = '['.escapeshellarg($this->options['extract']).'] ';
    }

    exec($this->magickCommands['convert'].' '.$command.' '.escapeshellarg($this->image).$extract.' '.escapeshellarg($thumbDest));
  }

  public function freeSource()
  {
    if (is_resource($this->source))
    {
      fclose($this->source);
    }
  }

  public function freeThumb()
  {
    return true;
  }

  public function getSourceMime()
  {
    return $this->sourceMime;
  }

}
