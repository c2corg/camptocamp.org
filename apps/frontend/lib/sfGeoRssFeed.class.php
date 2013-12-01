<?php

/**
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Francois Van Der Biest <francois.vanderbiest@camptocamp.com>
 */
 
class sfGeoRssFeed extends sfRssFeed
{
   
  /**
   * Returns the the current object as a valid RSS 1.0 XML feed.
   *
   * @return string A RSS 2.0 XML string.
   */
  public function toXml()
  {
    $this->initContext();
    $resource_url = 'http://'.sfConfig::get('app_static_version_host', sfConfig::get('app_classic_version_host'));
    $xml = array();
    $xml[] = '<?xml version="1.0" encoding="'.$this->getEncoding().'" ?>';
    $xml[] = '<rss version="'.$this->getVersion().'" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
                   xmlns:dc="http://purl.org/dc/elements/1.1/"
                   xmlns:atom="http://www.w3.org/2005/Atom"
                   xmlns:content="http://purl.org/rss/1.0/modules/content/"
                   >';
    $xml[] = '  <channel>';
    if ($this->getFeedUrl())
    {
        $xml[] = '    <atom:link href="'.$this->context->getController()->genUrl($this->getFeedUrl(), true).'" rel="self" type="application/rss+xml" />';
    }
    $xml[] = '    <title>'.$this->getTitle().'</title>';
    $xml[] = '    <link>'.$this->context->getController()->genUrl($this->getLink(), true).'</link>';
    $xml[] = '    <description>'.$this->getDescription().'</description>';
    $xml[] = '    <pubDate>'.strftime('%a, %d %b %Y %H:%M:%S %z', $this->getLatestPostDate()).'</pubDate>';
    $xml[] = '    <image>';
    $xml[] = '      <title>'.$this->getTitle().'</title>';
    $xml[] = '      <link>'.$this->context->getController()->genUrl($this->getLink(), true).'</link>';
    $xml[] = '      <url>'.$resource_url.'/static/images/logo_mini.png</url>';
    $xml[] = '    </image>';
    if ($this->getAuthorEmail())
    {
      $xml[] = '    <managingEditor>'.$this->getAuthorEmail().($this->getAuthorName() ? ' ('.$this->getAuthorName().')' : '').'</managingEditor>';
    }
    if (!$this->getAuthorEmail() && $this->getAuthorName())
    {
      $xml[] = '    <dc:creator><![CDATA['.$this->getAuthorName().']]></dc:creator>';
    }    if ($this->getLanguage())
    {
      $xml[] = '    <language>'.$this->getLanguage().'</language>';
    }
    if(strpos($this->version, '2.') !== false)
    {
      if(is_array($this->getCategories()))
      {
        foreach ($this->getCategories() as $category)
        {
          $xml[] = '    <category>'.$category.'</category>';
        }
      }
    }
    $xml[] = implode("\n", $this->getFeedElements());
    $xml[] = '  </channel>';
    $xml[] = '</rss>';

    return implode("\n", $xml);
  }

  /**
   * Returns an array of <item> tags corresponding to the feed's items.
   *
   * @return string An list of <item> elements.
   */
  protected function getFeedElements()
  {
    $xml = array();
    foreach ($this->getItems() as $item)
    {
      $xml[] = '    <item>';
      $xml[] = '      <title>'.htmlspecialchars($item->getTitle()).'</title>';
      $xml[] = '      <link>'.$this->context->getController()->genUrl($item->getLink(), true).'</link>';
      if ($item->getDescription())
      {
        $xml[] = '      <description>'.htmlspecialchars($item->getDescription()).'</description>';
      }
      if ($item->getContent())
      {
        $xml[] = '      <content:encoded><![CDATA['.$item->getContent().']]></content:encoded>';
      }
      if(strpos($this->version, '2.') !== false)
      {
        if ($item->getUniqueId())
        {
          $xml[] = '      <guid isPermaLink="false">'.htmlspecialchars($item->getUniqueId()).'</guid>';
        }
  
        // author information
        if ($item->getAuthorEmail())
        {
          $xml[] = sprintf('      <author>%s%s</author>', $item->getAuthorEmail(), ($item->getAuthorName()) ? ' ('.$item->getAuthorName().')' : '');
        }
        elseif ($item->getAuthorName())
        {
          $xml[] = sprintf('      <dc:creator><![CDATA[%s]]></dc:creator>', $item->getAuthorName());
        }
        if ($item->getPubdate())
        {
          $xml[] = '      <pubDate>'.strftime('%a, %d %b %Y %H:%M:%S %z', $item->getPubdate()).'</pubDate>';
        }
        if (is_string($item->getComments()))
        {
          $xml[] = '      <comments>'.htmlspecialchars($item->getComments()).'</comments>';
        }
  
        // enclosure
        if ($enclosure = $item->getEnclosure())
        {
          $enclosure_attributes = sprintf('url="%s" length="%s" type="%s"', $enclosure->getUrl(), $enclosure->getLength(), $enclosure->getMimeType());
          $xml[] = '      <enclosure '.$enclosure_attributes.'></enclosure>';
        }
  
        // categories
        if(is_array($item->getCategories()))
        {
          foreach ($item->getCategories() as $category)
          {
            $xml[] = '      <category>'.$category.'</category>';
          }
        }
        
        // added for GeoRSS:
        $lat = $item->getLatitude();
        if (!empty($lat) && is_numeric($lat))
        {
            $xml[] = '      <geo:lat>'.$lat.'</geo:lat>';
        }
        $lon = $item->getLongitude();
        if (!empty($lon) && is_numeric($lon))
        {
            $xml[] = '      <geo:long>'.$lon.'</geo:long>';
        }
        
      }
      $xml[] = '    </item>';
    }

    return $xml;
  }
  
  /**
   * Adds one item to the feed, based on an associative array
   *
   * @param array an associative array
   *
   * @return sfFeed the current sfFeed object
   */
  public function addItemFromArray($item_array)
  {
    $this->items[] = new sfGeoFeedItem($item_array);
    
    return $this;
  }
    
  /**
   * Adds one item to the feed
   *
   * @param sfFeedItem an item object
   *
   * @return sfFeed the current sfFeed object
   */
  public function addItem($item)
  {
    if (!($item instanceof sfGeoFeedItem))
    {
      // the object is of the wrong class
      $error = 'Parameter of addItem() is not of class sfGeoFeedItem';

      throw new Exception($error);
    }
    $item->setFeed($this);
    $this->items[] = $item;
    
    return $this;
  }
  
}

?>
