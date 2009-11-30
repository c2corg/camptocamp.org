<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMapFishActions contains shortcuts for redering JSON with status code
 *   and 204 empty page
 *
 * @package     sfMapFishPlugin
 * @author      Camptocamp <info@camptocamp.com>
 */
class sfMapFishActions extends sfActions
{

  /**
   * Returns JSON response with correct content-type and passed status code
   *
   * @param string $JSON A JSON string
   * @param integer $statusCode A status code (default to 200)
   *
   * @return sfView::NONE
   */
  public function renderJSON($JSON, $statusCode=200)
  {
    $r = $this->getResponse();
    $r->clearHttpHeaders();
    $r->setStatusCode($statusCode);
    $r->setContentType('application/json');
    
    return $this->renderText($JSON);
  }

  /**
   * Return HTTP code 204 (No-content)
   *
   * @return sfView::NONE
   */
  public function forward204()
  {
    $r = $this->getResponse();
    $r->clearHttpHeaders();
    $r->setStatusCode(204);
    
    return sfView::NONE;
  }

}
