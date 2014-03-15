<?php
/**
 * Class for GIS SQL QUERIES management
 *
 * @package c2c
 * @author Mickael Kurmann
 * @version $Id: $
 */

class gisQuery
{
    const tolerance_pixels = 11.;
    
    public static function getDistanceBetween($table, $pt1, $pt2)
    {

        $sql = 'SELECT (distance_sphere(setsrid(makepoint(s1.lon, s1.lat), 4326), '.
                'setsrid(makepoint(s2.lon, s2.lat), 4326))) AS distance '.
                'FROM '.$table.' AS s1, '.$table.' AS s2 '.
                'WHERE s1.id = ? AND s2.id = ?';
                
        $rs = sfDoctrine::connection()
                        ->standaloneQuery($sql, array($pt1, $pt2))
                        ->fetchObject();
        
        return $rs->distance;
    }
    
    public static function getQueryByBbox($bbox, $field = 'geom', $module = null)
    {
        /* reformat the bbox, from "minx,miny,maxx,maxy" to "minx miny, maxx maxy" */
        $bbox_array = explode(",", $bbox);
        $reformatted_bbox = "$bbox_array[0] $bbox_array[1], $bbox_array[2] $bbox_array[3]";
        $geom = "setSRID('BOX3D($reformatted_bbox)'::box3d, 900913)";

        // for modules with multipoint geometry, we cannot simply use &&, but we also need
        // to check for intersection
        if (!isset($module))
        {
            $module = sfContext::getInstance()->getModuleName();
        }
        switch ($module)
        {
            case 'maps':
            case 'areas':
            case 'outings':
            case 'routes':
                $multipoint = true;
                break;
            default:
                $multipoint = false;
        }

        // note: ? doesn't work because of the simple quotes around BOX3D()
        return array(
            'where_string' => $field . " && $geom" . ($multipoint ? "AND ST_Intersects($field, $geom)" : ''),
            'where_params' => array()
        );
    }
    
    public static function queryById($id, $user_prefered_langs) 
    {
        $sql = "SELECT
                  i.id, i.culture, i.name, d.module
                FROM
                  documents_i18n i LEFT JOIN documents d USING(id)
                WHERE
                   i.id = $id";

        $results = sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
        
        // build the actual results based on the user's prefered language
        $ref_culture_rank = 10; // fake high value
        foreach ($results as $result)
        {
            $tmparray = array_keys($user_prefered_langs, $result["culture"]); 
            $rank = array_shift($tmparray);
            if ($rank < $ref_culture_rank)
            {
                $name = $result;
                $ref_culture_rank = $rank;
            }
        }
        
        return array(0 => $name);
    }

    // this one hydrates best name
    // not used anymore ? 
    // FIXME: to delete ?
    public static function getAreaContaining($object_id, $area_type, $user_prefered_langs)
    {
        $sql = '(SELECT geom FROM documents WHERE id = ?)';

        $sql1 = 'SELECT id FROM areas a WHERE a.area_type = ?' .
                " AND intersects(buffer(geom, 200), $sql)" .
                " AND geom && $sql".
                ' LIMIT 1';

        $sql2 = "SELECT i.id, i.name, i.culture FROM areas_i18n i WHERE id IN ($sql1)";
                
        $results = sfDoctrine::connection()
                        ->standaloneQuery($sql2, array($area_type, $object_id, $object_id))
                        ->fetchAll(); 

        // build the actual results based on the user's prefered language
        $ref_culture_rank = 10; // fake high value
        foreach ($results as $result)
        {
            $tmparray = array_keys($user_prefered_langs, $result["culture"]); 
            $rank = array_shift($tmparray);
            if ($rank < $ref_culture_rank)
            {
                $name = $result["name"];
                $ref_culture_rank = $rank;
            }
        }
        return !empty($results) ? array('id' => $result["id"], 'name' => $name): null;
    }

    // this one returns an array with area_type and area_id of associated areas.
    // by default, it scans for inclusion in areas of type 1, 2 and 3
    public static function getAreasContaining($id)
    {
        $geom = "(SELECT Force_2d(geom) FROM documents WHERE id = ?)";
        
        $sql = 'SELECT id, area_type AS type FROM areas WHERE' .
                " intersects(buffer(geom, 200), $geom)" .
                " AND geom && $geom";
                
        return sfDoctrine::connection()
                        ->standaloneQuery($sql, array($id, $id))
                        ->fetchAll(); 
    }

    public static function getMapsContaining($id)
    {
        $geom = "(SELECT Force_2d(geom) FROM documents WHERE id = ?)";
        
        $sql = 'SELECT id FROM maps WHERE' .
                " intersects(geom, $geom)" .
                " AND geom && $geom";
                
        return sfDoctrine::connection()
                        ->standaloneQuery($sql, array($id, $id))
                        ->fetchAll(); 
    }

    public static function getEWKT($id, $trim = false, $module = null, $version = null, $simplify_tolerance = null)
    {
        $values[] = $id;
        $table = !empty($module) ? $module : 'documents';
        $geom = is_int($simplify_tolerance) ? 'Simplify(geom, '.$simplify_tolerance.')' : 'geom';
        if (!empty($version))
        {
            $table = 'app_' . $table . '_archives';
            $sql = "SELECT asEWKT(Transform($geom, 4326)) AS ewkt FROM $table AS d, app_documents_versions AS v WHERE d.document_archive_id = v.document_archive_id AND d.id = ? AND v.version = ?";
            $values[] = $version;
        }
        else
        {
            $sql = "SELECT asEWKT(Transform($geom, 4326)) AS ewkt FROM $table AS d WHERE d.id = ?";
        }
        
        $rs = sfDoctrine::connection()
                        ->standaloneQuery($sql, $values)
                        ->fetchObject();
                        
        $out = $rs->ewkt;
                        
        if ($trim) 
        {
            // we only keep coordinates with parenthesis
            $begin = strpos($out, '(');
            $end = strrpos($out, ')') + 1;
            return substr($out, $begin, $end - $begin);
        }
        else
        {
            return $out;
        }
    }

    public static function getGeoJSON($id, $module = null, $version = null, $simplify_tolerance = null, $maxdecimaldigits = 15)
    {
        $values[] = $id;
        $table = !empty($module) ? $module : 'documents';
        $geom = is_int($simplify_tolerance) ? 'Simplify(geom, '.$simplify_tolerance.')' : 'geom';
        if (!empty($version))
        {
            $table = 'app_' . $table . '_archives';
            $sql = "SELECT ST_AsGeoJSON(Transform($geom, 4326), $maxdecimaldigits) AS geojson FROM $table AS d, app_documents_versions AS v WHERE d.document_archive_id = v.document_archive_id AND d.id = ? AND v.version = ?";
            $values[] = $version;
        }
        else
        {
            $sql = "SELECT ST_AsGeoJSON(Transform($geom, 4326), $maxdecimaldigits) AS geojson FROM $table AS d WHERE d.id = ?";
        }

        $rs = sfDoctrine::connection()
                        ->standaloneQuery($sql, $values)
                        ->fetchObject();
        return  $rs->geojson;
    }

    public static function getBox2d($id, $module = null)
    {
        $values[] = $id;
        $table = !empty($module) ? $module : 'documents';
        $sql = "SELECT Box2D(geom) FROM $table AS d WHERE d.id = ?";

        $rs = sfDoctrine::connection()
                        ->standaloneQuery($sql, $values)
                        ->fetchObject();

        $out = $rs->box2d;

        $begin = strpos($out, '(') + 1;
        $end = strrpos($out, ')');
        return substr($out, $begin, $end - $begin);
    }

    // creates geographic associations of document $id with maps and areas
    // if $delete_old is true, then delete previous geo associations concerning doc $id
    public static function createGeoAssociations($id, $delete_old = true, $associate_with_maps = true)
    {
        $nb_created = 0;

        // if associations with areas for current doc already existed, delete them
        if ($delete_old)
        {
            $deleted = GeoAssociation::deleteAllFor($id, array('dr', 'dc', 'dd', 'dv', 'dm'));
            c2cTools::log("executeEdit: deleted $deleted geom associations for document $id");
        }
                    
        // compute new associations
        $areas = self::getAreasContaining($id);
        // perform association with these areas.
        foreach ($areas as $area)
        {                        
            switch ($area['type']) 
            {
                case 1: // range
                    $type = 'dr';
                    break;
                case 2: // country
                    $type = 'dc';
                    break;
                case 3: // dept
                    $type = 'dd';
                    break;
                case 4: // valley
                    $type = 'dv';
                    break;
            }
                        
            $a = new GeoAssociation();
            $a->doSaveWithValues($id, $area['id'], $type); // main, linked, type
            $nb_created++;
        }
        
        if ($associate_with_maps)
        {
            // compute new associations
            $maps = self::getMapsContaining($id);
            // perform association with these maps.
            foreach ($maps as $map)
            {
                $a = new GeoAssociation();
                $a->doSaveWithValues($id, $map['id'], 'dm'); // main, linked, type
                $nb_created++;
            }
        }
        
        return $nb_created;
    }

}
