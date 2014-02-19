<?php
/**
 * Class ParseGeo
 * $Id: ParseGeo.class.php 2421 2007-11-26 13:26:48Z alex $
 */

// FIXME: test with several gpx and kml files to test whether robust.

class ParseGeo
{
    /*
     * filter_gpx looks for filter switches in config files and applies these filters to input gps file
     * eg: - distance filter to prebent too many points
     *     - hdop filter to remove very unprecise points
     * it needs gpsbabel installed to work
     */
    public static function filterGpx($path)
    {
        $filter_distance = (sfConfig::get('app_gpx_filter_distance_switch', true)) ?
                            '-x position,distance=' . sfConfig::get('app_gpx_filter_distance_value', 5).'m' :
                            '' ;
            
        $filter_hdop = (sfConfig::get('app_gpx_filter_hdop_switch', true)) ? 
                            '--x discard,hdop=' . sfConfig::get('app_gpx_filter_hdop_value', 4) :
                            '' ;
                
        exec("gpsbabel -t $filter_distance $filter_hdop -i gpx -f $path -o gpx -F $path");
                
        c2cTools::log("File filtered with gpsbabel");
    }

    /*
     * gpx2wkt does what its name implies : 
     * it takes a gpx file, parses it for tracks and returns an equivalent WKT LINESTRING 
     * by default the returned WKT is 3D. 
     * If $dim = 4 then it returns a 4D WKT (outings)
     */
    public static function gpx2wkt($path, $dim = 3)
    {
        $xml = c2cTools::simplexmlLoadFile($path);
        // TODO: handle files with only one waypoint for point update ?
        // TODO: handle multilines geometries ?
        $i = 0;
        $wkta = array();
        // we merge all tracks and track segments together.
        // this is useful because some gps generate a new trkseg when gps signal is lost (eg: garmin units) 
        foreach ($xml->trk as $trk) 
        {
            foreach ($trk->trkseg as $trkseg) 
            {
                foreach ($trkseg->trkpt as $pt) 
                {
                    $_ll = $pt['lon'] . ' ' . $pt['lat'];
                    switch ($dim)
                    {
                        case 2:
                            $wkta[] = $_ll;
                            break;
                        case 3:
                            $wkta[] = ($pt->ele) ? $_ll. ' ' . round($pt->ele) : $_ll. ' 0' ;
                            break;
                        case 4:
                            // converts 2007-07-12T06:55:21Z into absolute unix time for easy storage
                            $wkta[] = (($pt->ele) ? $_ll. ' ' . round($pt->ele) : $_ll. ' 0') . ' ' . 
                                      (($pt->time && strtotime($pt->time) !== false) ? strtotime($pt->time) : '0') ;
                            break;
                    }
                    $i++;
                }
            }
        }
        // we also look for routes
        foreach ($xml->rte as $rte)
        {
            foreach ($rte->rtept as $pt)
            {
                $_ll = $pt['lon'] . ' ' . $pt['lat'];
                switch ($dim)
                {
                    case 2:
                        $wkta[] = $_ll;
                        break;
                    case 3:
                        $wkta[] = ($pt->ele) ? $_ll. ' ' . round($pt->ele) : $_ll. ' 0' ;
                        break;
                    case 4:
                        // converts 2007-07-12T06:55:21Z into absolute unix time for easy storage
                        $wkta[] = (($pt->ele) ? $_ll. ' ' . round($pt->ele) : $_ll. ' 0') . ' ' .
                                  (($pt->time) ? strtotime($pt->time) : '0') ;
                        break;
                }
                $i++;
            }
        }
        
        if ($i)
        {
            c2cTools::log("gpx2wkt : WKT has been generated with $i points");
            return 'LINESTRING(' . implode(',',$wkta) . ')';
        }
        else
        {
            c2cTools::log("gpx2wkt : no track or route found");
            return false;
        }
    }
   
    /**
     * getKmlCoordinates is used by kml2wkt to transform a coordinates string into another one, suitable for WKT generation
     * If $dim = 4, then time field will be zero-padded.
     * it has to handle data like 5.937372,45.640974 5.937182,45.642683 5.937189,45.642667 
     * or 5.937372,45.640974       \n     5.937182,45.642683          \n        5.937189,45.642667 \n
     * or 5.937372,45.640974 \n 5.937182,45.642683 \n 5.937189,45.642667
     *
     * it must return a string such as:
     *    5.937372 45.640974, 5.937182 45.642683, 5.937189 45.642667 if 2D output
     * or 5.937372 45.640974 130, 5.937182 45.642683 128, 5.937189 45.642667 125 if 3D output
     * or 5.937372 45.640974 130 0, 5.937182 45.642683 128 0, 5.937189 45.642667 125 0 if 4D output
     */   
    private static function getKmlCoordinates($input, $dim = 3)
    {
        // convert all \n \r\n and \n into spaces:
        $str = str_replace(array("\r\n", "\r", "\n"), ' ', $input);
        // trim whole string:
        $str = trim($str);
        // replace one or successive spaces by one '|':
        $str = preg_replace('/\s+/', '|', $str);
        // convert all ',' into spaces:
        $str = str_replace(array(','), ' ', $str);
        // explode on '|':
        $a = explode('|', $str); 
        // we get : 
        // (3D) array('5.937372 45.640974 130.25', 5.937372 45.640974 130.23') or 
        // (2D) array('5.937372 45.640974', 5.937372 45.640974')
        // next step is to determine dimension of input KML (2 or 3) and round Z if exists.
        
        // free memory:
        unset($str);
 
        $vertexes = array();
        foreach ($a as $vertex)
        {
            $_vertex = explode(' ', $vertex);
            $nb_dims_vertex = count($_vertex);
            // this might be 2, 3 or 4 and we must output data with $dim dimensions (2, 3 or 4) => zero padding or truncating ...
            switch ($dim - $nb_dims_vertex) // -2, -1, 0, 1, 2
            {
                case 2: // 4D output and 2D input 
                    $vertexes[] = $vertex . ' 0 0';
                    break;
                case 1: // 4D output and 3D input or 3D output and 2D input
                    $vertexes[] = ($nb_dims_vertex == 2) ? 
                                            $vertex . ' 0' : 
                                            $_vertex[0] . ' ' . $_vertex[1] . ' ' . round($_vertex[2]) . ' 0' ;
                    break;
                case 0: // same dims for output and input
                    $_vertex[2] = round($_vertex[2]);
                    $vertexes[] = implode(' ', $_vertex);
                    break;
                case -1: // 2D output and 3D input (only, because 4D input not -yet- implemented)
                    $vertexes[] =  $_vertex[0] . ' ' . $_vertex[1] ;
                    break;
                case -2: // 2D output and 4D input 
                    $vertexes[] = $_vertex[0] . ' ' . $_vertex[1] ;
                    break;
            }
        }
        return implode(', ', $vertexes);
    }
    
    /**
     * kml2wkt does what its name implies : 
     * it takes a kml file, parses it for linestrings and returns an equivalent WKT LINESTRING
     *
     * The returned WKT can be 2, 3 or 4D. 
     * If does not really handle 4D parsing (beyond scope IMO) => KML import is restricted to routes (outings will only support GPX input)
     * If $dim = 4, then time field will be zero-padded.
     */
    public static function kml2wkt($path, $dim = 3)
    {
        // FIXME: document structure is very user customizable, and thus this function is not very robust ...
        // a solution would be to extract points linked to a linestring (if these points are present), and to sort them by date to build the linestring. 
        
        $xml = c2cTools::simplexmlLoadFile($path);
        // TODO: handle files with Document>NetworkLink>Url>href : wget content... (mymaps generates these ones)
        // TODO: handle files with one waypoint for point update ?
        // TODO : handle multilines geometries ?
        
        $i = 0;
        $wkta = array();
        
        if ($pm = $xml->Document->Placemark)
        {
            // this is a simple kml 
            // (for instance, one made with google "my maps")
            // in which these is no folder.
            c2cTools::log("ParseGeo::kml2wkt($path, $dim) with xml->Document->Placemark");
            return 'LINESTRING(' . self::getKmlCoordinates($pm->LineString->coordinates, $dim). ')';
        }
        elseif ($geom_folders = $xml->Document->Folder)
        {
            // this is a "complex" KML with several folders (typical output of gpsbabel from gpx)
            $trk_coords = array();
            // kml file may contain three geom folders : Waypoints, Tracks and Routes
            foreach ($geom_folders as $folder)
            {
                // we're just looking for tracks :
                if ($folder->name == 'Tracks')
                {
                    // there might be subfolders if gps signal has been interrupted 
                    // we merge these folders together to build a single track.
                    foreach ($folder->Folder as $track) 
                    {
                        $trk_coords[] = self::getKmlCoordinates($track->Placemark->LineString->coordinates, $dim);
                    }
                }
            }
            c2cTools::log("ParseGeo::kml2wkt($path, $dim) with xml->Document->Folder");
            return 'LINESTRING(' . implode(', ', $trk_coords) . ')';
        }
        else
        {
            c2cTools::log("kml2wkt : no track found");
            return false;
        }
    }
    
    /**
     * getCumulatedAltDiffFromWkt computes the cumulated difference in altitude from a WKT LINESTRING
     */    
    public static function getCumulatedHeightDiffFromWkt($wkt) 
    {
        if (substr($wkt,0,10) != 'LINESTRING') 
        {
            return array(null, null);
        }
        
        $begin = strpos($wkt, '(') + 1;
        $end = strrpos($wkt, ')');
        $str = substr($wkt, $begin, $end - $begin);
        $vertexes = explode(',', $str); 
        
        $cumulated_diff_up = 0;
        $cumulated_diff_down = 0;
        
        $_vertex0 = explode(' ', $vertexes[0]);
        $old_elevation = $_vertex0[2];
        
        foreach ($vertexes as $vertex)
        {
            $_vertex = explode(' ', $vertex);
            $elevation = $_vertex[2];
            
            $diff = $elevation - $old_elevation;
            if ($diff > 0)
            {
                $cumulated_diff_up += $diff;
            }
            else
            {
                $cumulated_diff_down += $diff;
            }
            $old_elevation = $elevation;
        }
        
        return array('up' => $cumulated_diff_up, 'down' => -$cumulated_diff_down);
    }

}
