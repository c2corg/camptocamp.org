<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMapFishPlugin configuration.
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
  
    $this->dispatcher->connect(
      'request.method_not_found',
     array('sfMapFishRequest', 'listenToMethodNotFound')
    );
    
    $this->configureDoctrine();
    
    sfConfig::set(
      'mf_print_jar', 
      sfConfig::get('sf_plugins_dir').'/sfMapFishPlugin/lib/print/print-standalone.jar'
    );
    sfConfig::set(
      'mf_print_cfg', 
      sfConfig::get('sf_config_dir').'/print.yml'
    );
  }

  /**
   * Change the base class name for records
   */
  public function configureDoctrine()
  {
    $options = array(
      'baseClassName' => 'sfMapFishRecord' ,
#      'baseTableName' => 'sfMapFishTable' # as soon as patch for #1976 pass ( http://trac.doctrine-project.org/ticket/1976 )
    );
    sfConfig::set('doctrine_model_builder_options', $options);
  }

}

class sfMapFishRequest
{

  static public function listenToMethodNotFound(sfEvent $event)
  {
    /**
     * retrieve raw post data
     */
    if ($event['method']==='getRawBody')
    {
      $event->setProcessed(true);
      $event->setReturnValue(file_get_contents('php://input'));
    }

    /**
     * remove parameter from request parameterHolder
     */
    if ($event['method']==='removeParameter')
    {
      $ph = $event->getSubject()->getParameterHolder();
      foreach ($event['arguments'] as $parameter)
      {
        $ph->remove($parameter);
      }
      $event->setProcessed(true);
    }
  }

}
