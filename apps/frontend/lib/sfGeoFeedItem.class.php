<?php

/**
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Van Der Biest <francois.vanderbiest@camptocamp.com>
 */
 
class sfGeoFeedItem extends sfFeedItem
{
  private
   $longitude,
   $latitude;

  /**
   * Sets the feed item parameters, based on an associative array
   *
   * @param array an associative array
   *
   * @return sfFeedItem the current sfFeedItem object
   */
  public function initialize($item_array)
  {
    parent::initialize($item_array);
    $this->setLongitude(isset($item_array['longitude']) ? $item_array['longitude'] : '');
    $this->setLatitude(isset($item_array['latitude']) ? $item_array['latitude'] : '');
    return $this;
  }

  public function setLongitude ($longitude)
  {
    $this->longitude = $longitude;
  }

  public function getLongitude ()
  {
    return $this->longitude;
  }

  public function setLatitude ($latitude)
  {
    $this->latitude = $latitude;
  }

  public function getLatitude ()
  {
    return $this->latitude;
  }
}

?>