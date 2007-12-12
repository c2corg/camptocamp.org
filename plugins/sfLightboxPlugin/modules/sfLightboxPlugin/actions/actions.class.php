<?php

/**
 * sfLightboxPlugin actions.
 *
 * @package    sfLightboxPlugin
 * @author     Vernet Loic aka COil <qrf_coil[at]yahoo[dot]fr>
 * @since      1.0.5 - 27 apr 2007
 */

class sfLightboxPluginActions extends sfActions
{

  /**
   * index action
   * 
   * @see executeTest()
   */
  public function executeIndex()
  {
    $this->forward($this->getModuleName(), 'test');
  }

  /**
   * Test of sfLightboxPlugin
   * 
   * @see _test.php
   */
  public function executeTest()
  {
  }

  /**
   * Test of modalbox popup
   */
  public function executeModal()
  {
  }

}