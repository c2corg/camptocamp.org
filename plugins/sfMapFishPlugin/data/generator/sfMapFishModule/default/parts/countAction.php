<?php echo '
  /**
   * Returns number of '.$this->getModelClass().' for request
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeCount(sfWebRequest $request)
  {
    return $this->renderText(Doctrine::getTable('<?php echo $this->getModelClass() ?>')->countByProtocol($request));
  }
