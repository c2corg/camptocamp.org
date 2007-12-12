<?php
/*
 * This file is part of the sfDoctrine package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony.plugins
 * @subpackage sfDoctrine
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineQueryLogger.class.php 4416 2007-06-26 21:54:15Z subzero2000 $
 */

class sfDoctrineQueryLogger extends Doctrine_EventListener
{
  protected $connection = null;
  protected $encoding = 'UTF8';

  public function preExecute(Doctrine_Event $event)
  {
    $log = '{sfDoctrine Pre-execute} executeQuery : '.$event->getQuery();
    if ($params = $event->getParams())
    {
      $log .= ' - ('.implode(', ',$params) . ' )';
    }
    sfContext::getInstance()->getLogger()->log($log);
    $sqlTimer = sfTimerManager::getTimer('Database (Doctrine)');
  }
  
  public function postExecute(Doctrine_Event $event)
  {
    sfTimerManager::getTimer('Database (Doctrine)')->addTime();
  }

  public function prePrepare(Doctrine_Event $event)
  {
    $log = '{sfDoctrine Pre-execute} prepareQuery : '.$event->getQuery();
    if ($params = $event->getParams())
    {
      $log .= ' - ('.implode(', ',$params) . ' )';
    }
    sfContext::getInstance()->getLogger()->log($log);
    $sqlTimer = sfTimerManager::getTimer('Database (Doctrine)');
  }
  
  public function postPrepare(Doctrine_Event $event)
  {
    sfTimerManager::getTimer('Database (Doctrine)')->addTime();
  }

  public function preStmtExecute(Doctrine_Event $event)
  {
    $log = '{sfDoctrine Pre-execute} executePreparedQuery : '.$event->getQuery();
    if ($params = $event->getParams())
    {
      $log .= ' - ('.implode(', ',$params) . ' )';
    }
    sfContext::getInstance()->getLogger()->log($log);
    $sqlTimer = sfTimerManager::getTimer('Database (Doctrine)');
  }
  
  public function postStmtExecute(Doctrine_Event $event)
  {
    sfTimerManager::getTimer('Database (Doctrine)')->addTime();
  }

  public function preQuery(Doctrine_Event $event)
  {
    sfContext::getInstance()->getLogger()->log('{sfDoctrine Query} executeQuery : '.$event->getQuery());
    $sqlTimer = sfTimerManager::getTimer('Database (Doctrine)');
  }
  
  public function postQuery(Doctrine_Event $event)
  {
    sfTimerManager::getTimer('Database (Doctrine)')->addTime();
  }
}
