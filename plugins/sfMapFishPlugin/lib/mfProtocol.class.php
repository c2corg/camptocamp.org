<?php

/**
 * mfProtocol is used to filter features according to MapFish Protocol
 *
 * @package    symfony
 * @subpackage mapfish
 * @author     Camptocamp <info@camptocamp.com>
 *
 */
class mfProtocol
{

  /**
   *
   *
   * @param Doctrine_Collection $features
   * @param sfWebRequest $request
   *
   * @return Doctrine_Collection
   */
  static public function filter(Doctrine_Collection $features, sfWebRequest $request)
  {
    if (($no_geom=$request->hasParameter('no_geom')) || $request->hasParameter('attrs') )
    {
      foreach ($features as $feature)
      {
        if ($no_geom)
        {
          $feature->set(
            $feature->getTable()->getGeometryColumnName(),
            null
          );
        }
        if ($request->hasParameter('attrs'))
        {
          if (is_array($request->getParameter('attrs')))
          {
            $feature->setExportedProperties($request->getParameter('attrs'));
          }
          else
          {
            $feature->setExportedProperties(explode(',', $request->getParameter('attrs')));
          }
        }
      }
    }
    return $features;
  }

}
