<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMapFishRouteCollection represents a collection of routes bound to Doctrine objects.
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishRouteCollection extends sfObjectRouteCollection
{
  protected
    $routeClass = 'sfMapFishRoute';
}
