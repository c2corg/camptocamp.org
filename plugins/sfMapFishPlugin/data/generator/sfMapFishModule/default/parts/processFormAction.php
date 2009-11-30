<?php echo '
  /**
   * Saves '.$this->getModelClass().' and its geometry
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   *
   * @return Doctrine_Record
   */
';?>
  protected function processForm(Feature $feature, sfForm $form)
  {
    $c = Doctrine_Manager::getInstance()->getCurrentConnection();
    $c->beginTransaction();

    if (!$form->getObject()->isNew())
    {
      $feature->setProperty(
        Doctrine::getTable($form->getModelName())->getIdentifier(),
        $feature->getId()
      );
    }

    if (!$form->bindAndSave($feature->getProperties()) || !$form->getObject()->updateGeometry($feature->getGeometry()))
    {
      $c->rollback();
      return false;
    }

    $c->commit();
    
    return GeoJSON::loadFrom(
      Doctrine::getTable('<?php echo $this->getModelClass() ?>')->geoFind($form->getObject()->getPrimaryKey()),
      new GeoJSON_Doctrine_Adapter
    );
  }
