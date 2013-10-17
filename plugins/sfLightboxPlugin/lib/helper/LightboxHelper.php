<?php

  /**
   * @package sfLightboxPlugin
   * 
   * @author COil <qrf_coil@yahoo.fr>
   * @since  1.0.0 - 5 feb 2007
   * @version 1.0.4
   * 
   * Original library by
   * @author Lokesh Dhakar
   * http://www.huddletogether.com/projects/lightbox2/
   * 
   * Lightbox modalbox modification by Demental 
   * http://demental.info/blog/index.php?post/2007/01/11/75-introducing-modalbox
   */

  /**
   * Returns an image link to use the lightbox function for 1 image
   *
   * @param string $thumbnail Name of thumbnail to display
   * @param string $image     Name of full size image to display
   * @param array  $options   Options of the link
   * 
   * @author COil <qrf_coil@yahoo.fr>
   * @since 1.0.0 - 5 feb 2007
   *
   * 
   */
  function light_image($thumbnail, $image, $link_options = array(), $image_options = array())
  {
    _addLbRessources();
    $link_options = _parse_attributes($link_options);
    $image_options = _parse_attributes($image_options);    

    // Lightbox specific
    $link_options['rel'] = isset($link_options['rel']) ? $link_options['rel'] : 'lightbox';

    return link_to(image_tag($thumbnail, $image_options), image_path($image, true), $link_options);
    
  }
  
  /**
   * Returns image links to use the lightbox slideshow function
   *
   * @param array  $images An associative array with the name of the thumbnail
   * as key and the image to display as the value
   * @param array  $options['slide'] Name of the lightbox slide, 'slide' by
   * default
   * @param array $options Others options of the link
   * 
   * @author COil <qrf_coil@yahoo.fr>
   * @since 1.0.0 - 5 feb 2007
   */
  function light_slideshow($images, $link_options = array())
  {
    _addLbRessources();
    $link_options = _parse_attributes($link_options);
    // Lightbox specific
    $link_options['rel'] = 'lightbox['. ((isset($options['slidename']) ? $options['slidename'] : 'slide')) .']';
    unset($link_options['slidename']);
    $value = '';

    if ($images) {
      $title = $link_options['title'];
      foreach ($images as $image) {
        $image['options']['rel'] = $link_options['rel'];
        $link_options['title'] = isset($image['options']['title']) && $image['options']['title'] ? $image['options']['title'] : $title;
        $value .= light_image($image['thumbnail'], $image['image'], $link_options, $image['options']);
      }
    }
    return $value;
  }  
  
  /**
   * Private function that adds the lightbox ressources
   * 
   * @access private
   * @author COil
   * @since 1.0.0 - 5 feb 2007
   */
  function _addLbRessources()
  {
    // Prototype & scriptaculous: no need since loaded each time for menu
    $response = sfContext::getInstance()->getResponse();
    //$response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/prototype', 'head_first');
    //$response->addJavascript(sfConfig::get('sf_prototype_web_dir'). '/js/effects', 'head_last');

    // Lightbox specific
    $response->addJavascript('/static/js/lightbox.js');
    $response->addStylesheet('/static/css/lightbox.css');      
  }
  
  /**
   * Adds Lightbox specific ressources
   */
  function addLbMinimalRessources()
  {
    $static_base_url = sfConfig::get('app_static_url');
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript('/static/js/lightbox.js');
    $response->addStylesheet('/static/css/lightbox.css');
  }
?>
