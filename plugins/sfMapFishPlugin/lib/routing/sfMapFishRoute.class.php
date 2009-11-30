<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMapFishRoute represents a route that is bound to a Doctrine class.
 *
 * A MapFish route can represent a single Doctrine object or a list of objects.
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishRoute extends sfDoctrineRoute
{

  public function getObjectsForParameters($parameters)
  {
    $request = sfContext::getInstance()->getRequest();
    $features = Doctrine::getTable($this->options['model'])
      ->filterByProtocol($request)
      ->execute();

    mfProtocol::filter($features, $request);

    if ($request->hasParameter('id'))
    {
      return $features->getFirst();
    }
    else
    {
      return $features;
    }
  }

}
