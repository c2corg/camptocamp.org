<?php echo '
  /**
   * Creates new '.$this->getModelClass().'(s) from FeatureCollection
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeCreate(sfWebRequest $request)
  {
    $features = GeoJSON::load($request->getRawBody());

    $c = Doctrine_Manager::getInstance()->getCurrentConnection();
    $c->beginTransaction();
    
    $updatedFeatures = new FeatureCollection;
    foreach ($features as $feature)
    {
      $object = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find((int) $feature->getId());
      $form = new <?php echo $this->getModelClass().'Form' ?>($object);
      if (!$feature = $this->processForm($feature, $form))
      {
        $c->rollback();
        return $this->renderJSON('{"success": false}', 500);
      }
      $updatedFeatures->addFeature($feature);
    }

    $c->commit();
    return $this->renderJSON(GeoJSON::dump($updatedFeatures), 201);
  }

