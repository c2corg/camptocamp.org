<?php echo '
  /**
   * Returns GeoJSON detail for one '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeShow(sfWebRequest $request)
  {
    $feature = GeoJSON::loadFrom($this->getRoute()->getObject(), new GeoJSON_Doctrine_Adapter);

    return $this->renderJSON(GeoJSON::dump($feature), 200);
  }
