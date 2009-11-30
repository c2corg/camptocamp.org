<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * MapFish module generator.
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishGenerator extends sfDoctrineGenerator
{

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfMapFishModule');
  }

}
