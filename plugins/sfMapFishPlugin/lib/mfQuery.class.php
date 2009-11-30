<?php

/**
 * Geometric queries helpers
 *
 */
class mfQuery extends Doctrine_Query
{
  /**
   * The name of the geometric column
   *
   * @var string
   */
  private $__geoColumn;

  /**
   * The epsg code of the geometric column
   *
   * @var string
   */
  private $__epsg;

  /**
   * Format string for geom column transform in select statement
   *
   * @var array
   */
  private static $__format = array(
    'ASTEXT' => ", ASTEXT(%s) %s, box2d(%s) bbox"
  );

  /**
   * Create the query & set the geo column
   *
   * @param string $column
   *
   * @return mfQuery
   */
  public static function create($column='the_geom', $epsg=4326)
  {
    $instance = new self();
    $instance->__geoColumn = $column;
    $instance->__epsg = $epsg;

    return $instance;
  }

  /**
   * sets the SELECT part of the query, and add geo column according to format
   *
   * @param string $string
   * @param mixed $append false or string : way to transform the geom
   *
   * @return mfQuery
   */
  public function select($string, $append='ASTEXT')
  {
    if ($append!==false)
    {
      $string .= sprintf(
        self::$__format[$append],
        $this->__geoColumn,
        $this->__geoColumn,
        $this->__geoColumn);
    }

    return parent::select($string);
  }
  
  /**
   * Builds a polygon WKT string from its coords
   *
   * @param float $left
   * @param float $bottom
   * @param float $right
   * @param float $top
   *
   * @return string
   */
  public function toPolygon($left, $bottom, $right, $top)
  {
    $A = $left.' '.$bottom;
    $B = $right.' '.$bottom;
    $C = $right.' '.$top;
    $D = $left.' '.$top;
    return "POLYGON(($A, $B, $C, $D, $A))";
  }
  
  /**
   * Add where clause to match geometries intersecting a box
   *
   * @param array $box
   * @param int $epsg
   * @param int $tolerance
   *
   * @return mfQuery
   */
  public function box($box, $epsg=null, $tolerance=0)
  { 
    $box = array_map('floatval', $box);
    $geometry = $bbox = $this->toPolygon($box[0], $box[1], $box[2], $box[3]);
    if ($tolerance > 0)
    {
      $bbox = $this->toPolygon(
        $box[0]-$tolerance, $box[1]-$tolerance, 
        $box[2]+$tolerance, $box[3]+$tolerance);
    }
    return $this->toQuery($geometry, $bbox, $epsg, $tolerance);
  }
  
  /**
   * Add where clause to get features within a distance from a point
   *
   * @param float $lon
   * @param float $lat
   * @param int $epsg
   * @param int $tolerance
   *
   * @return mfQuery
   */
  public function within($lon, $lat, $epsg=null, $tolerance=0)
  { 
    $geometry = $bbox = "POINT($lon $lat)";
    if ($tolerance > 0) 
    {
      $bbox = $this->toPolygon(
        $lon-$tolerance, $lat-$tolerance, 
        $lon+$tolerance, $lat+$tolerance);
    }

    return $this->toQuery($geometry, $bbox, $epsg, $tolerance);
  }

  /**
   * Add where clause with geometry intersects passed geometry
   *
   * @param string $geometry
   * @param string $bbox
   * @param int $epsg
   * @param int $tolerance
   *
   * @return mfQuery
   */
  public function toQuery($geometry, $bbox, $epsg, $tolerance)
  {
    $geom_column = (is_null($epsg)) ?
      $this->__geoColumn :
      'TRANSFORM('.$this->__geoColumn.', '.(int)$epsg.')';
    
    $epsg = ($epsg===null) ? $this->__epsg : $epsg;
    
    $this
      ->addWhere("$geom_column && GEOMETRYFROMTEXT(?, $epsg)", $bbox) 
      ->andWhere("DISTANCE(GEOMETRYFROMTEXT(?, $epsg), $geom_column) <= $tolerance", $geometry);
    
    return $this;
  }
  
}

