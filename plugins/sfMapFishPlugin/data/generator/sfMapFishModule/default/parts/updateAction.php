<?php echo '
  /**
   * Update a '.$this->getModelClass().' from passed feature
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeUpdate(sfWebRequest $request)
  {
    $form = new <?php echo $this->getModelClass().'Form' ?>($this->getRoute()->getObject());

    if ($feature = $this->processForm(GeoJSON::load($request->getRawBody()), $form))
    {
      return $this->renderJSON(GeoJSON::dump($feature), 201);
    }
    else
    {
      return $this->renderJSON('{"success": false}', 500);
    }
  }
