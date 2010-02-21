<?php

/*
 * Taken from mediawiki file includes/XmlTypeCheck.php
 * Licensed un der GPLv2+
 */
class XmlTypeCheck {
    /**
     * Will be set to true or false to indicate whether the file is
     * well-formed XML. Note that this doesn't check schema validity.
     */
    public $wellFormed = false;
    
    /**
     * Will be set to true if the optional element filter returned
     * a match at some point.
     */
    public $filterMatch = false;

    /**
     * Name of the document's root element, including any namespace
     * as an expanded URL.
     */
    public $rootElement = '';

    /**
     * @param $file string filename
     * @param $filterCallback callable (optional)
     *        Function to call to do additional custom validity checks from the
     *        SAX element handler event. This gives you access to the element
     *        namespace, name, and attributes, but not to text contents.
     *        Filter should return 'true' to toggle on $this->filterMatch
     */
    function __construct( $file, $filterCallback=null ) {
        $this->filterCallback = $filterCallback;
        $this->run( $file );
    }
    
    /**
     * Get the root element. Simple accessor to $rootElement
     */
    public function getRootElement() {
        return $this->rootElement;
    }

    private function run( $fname ) {
        $parser = xml_parser_create_ns( 'UTF-8' );

        // case folding violates XML standard, turn it off
        xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, false );

        xml_set_element_handler( $parser, array( $this, 'rootElementOpen' ), false );

        $file = fopen( $fname, "rb" );
        do {
            $chunk = fread( $file, 32768 );
            $ret = xml_parse( $parser, $chunk, feof( $file ) );
            if( $ret == 0 ) {
                // XML isn't well-formed!
                fclose( $file );
                xml_parser_free( $parser );
                return;
            }
        } while( !feof( $file ) );

        $this->wellFormed = true;

        fclose( $file );
        xml_parser_free( $parser );
    }

    private function rootElementOpen( $parser, $name, $attribs ) {
        $this->rootElement = $name;
        
        if( is_callable( $this->filterCallback ) ) {
            xml_set_element_handler( $parser, array( $this, 'elementOpen' ), false );
            $this->elementOpen( $parser, $name, $attribs );
        } else {
            // We only need the first open element
            xml_set_element_handler( $parser, false, false );
        }
    }
    
    private function elementOpen( $parser, $name, $attribs ) {
        if( call_user_func( $this->filterCallback, $name, $attribs ) ) {
            // Filter hit!
            $this->filterMatch = true;
        }
    }
}

/**
 * Return a rounded pixel equivalent for a labeled CSS/SVG length.
 * http://www.w3.org/TR/SVG11/coords.html#UnitIdentifiers
 *
 * Taken from mediawiki (file includes/ImageFunctions.php)
 * licensed under GPLv2+
 *
 * @param $length String: CSS/SVG length.
 * @param $viewportSize: Float optional scale for percentage units...
 * @return float: length in pixels
 */
function wfScaleSVGUnit( $length, $viewportSize=512 ) {
    static $unitLength = array(
        'px' => 1.0,
        'pt' => 1.25,
        'pc' => 15.0,
        'mm' => 3.543307,
        'cm' => 35.43307,
        'in' => 90.0,
        'em' => 16.0, // fake it?
        'ex' => 12.0, // fake it?
        ''   => 1.0, // "User units" pixels by default
        );
    $matches = array();
    if( preg_match( '/^\s*(\d+(?:\.\d+)?)(em|ex|px|pt|pc|cm|mm|in|%|)\s*$/', $length, $matches ) ) {
        $length = floatval( $matches[1] );
        $unit = $matches[2];
        if( $unit == '%' ) {
            return $length * 0.01 * $viewportSize;
        } else {
            return $length * $unitLength[$unit];
        }
    } else {
        // Assume pixels
        return floatval( $length );
    }
}


/*
 * Class taken from mediawiki
 * file includes/ImageFunctions.php
 * licensed under GPLv2+
 */
class XmlSizeFilter {
    const DEFAULT_WIDTH = 512;
    const DEFAULT_HEIGHT = 512;
    var $first = true;
    var $width = self::DEFAULT_WIDTH;
    var $height = self::DEFAULT_HEIGHT;
    function filter( $name, $attribs ) {
        if( $this->first ) {
            $defaultWidth = self::DEFAULT_WIDTH;
            $defaultHeight = self::DEFAULT_HEIGHT;
            $aspect = 1.0;
            $width = null;
            $height = null;
            
            if( isset( $attribs['viewBox'] ) ) {
                // min-x min-y width height
                $viewBox = preg_split( '/\s+/', trim( $attribs['viewBox'] ) );
                if( count( $viewBox ) == 4 ) {
                    $viewWidth = wfScaleSVGUnit( $viewBox[2] );
                    $viewHeight = wfScaleSVGUnit( $viewBox[3] );
                    if( $viewWidth > 0 && $viewHeight > 0 ) {
                        $aspect = $viewWidth / $viewHeight;
                        $defaultHeight = $defaultWidth / $aspect;
                    }
                }
            }
            if( isset( $attribs['width'] ) ) {
                $width = wfScaleSVGUnit( $attribs['width'], $defaultWidth );
            }
            if( isset( $attribs['height'] ) ) {
                $height = wfScaleSVGUnit( $attribs['height'], $defaultHeight );
            }
            
            if( !isset( $width ) && !isset( $height ) ) {
                $width = $defaultWidth;
                $height = $width / $aspect;
            } elseif( isset( $width ) && !isset( $height ) ) {
                $height = $width / $aspect;
            } elseif( isset( $height ) && !isset( $width ) ) {
                $width = $height * $aspect;
            }
            
            if( $width > 0 && $height > 0 ) {
                $this->width = intval( round( $width ) );
                $this->height = intval( round( $height ) );
            }
            
            $this->first = false;
        }
    }
}


class SVG
{
    public static  function getSize($filename)
    {
        $filter = new XmlSizeFilter();
        $xml = new XmlTypeCheck( $filename, array( $filter, 'filter' ) );
        if( $xml->wellFormed ) {
            return array($filter->width, $filter->height);
        }
        return false;
    }


    /**
     * Create the rasterized version of a SVG file
     * FIXME many things to be improved
     */
    public static function rasterize($path, $unique_filename, &$file_ext)
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
