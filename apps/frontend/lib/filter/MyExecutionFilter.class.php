<?php

/*
 * This file comes the symfony package (originally sfExecutionFilter.class.php) and has been reworked by me (vdb).
 */

class MyExecutionFilter extends sfExecutionFilter 
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain The filter chain
   *
   * @throws <b>sfInitializeException</b> If an error occurs during view initialization.
   * @throws <b>sfViewException</b>       If an error occurs while executing the view.
   */
  public function execute($filterChain)
  {
    // get the context and controller
    $context    = $this->getContext();
    $controller = $context->getController();

    // get the current action instance
    $actionEntry    = $controller->getActionStack()->getLastEntry();
    $actionInstance = $actionEntry->getActionInstance();

    // get the current action information
    $moduleName = $context->getModuleName();
    $actionName = $context->getActionName();

    // get the request method
    $method = $context->getRequest()->getMethod();

    $viewName = null;

    $statsdPrefix = c2cActions::statsdPrefix($moduleName, $actionName);

    if (sfConfig::get('sf_cache'))
    {
        // get current uri adapted for cache
        $uri = MyCacheFilter::getCurrentCacheUri();

        // best way would be to modify uri (and not the whole cache management system) 
        // but we have no way to extend getCurrentInternalUri method in sfRouting class just for cache 
      
      if (null !== $context->getResponse()->getParameter($uri.'_action', null, 'symfony/cache'))
      {
        // action in cache, so go to the view
        $viewName = sfView::SUCCESS;
      }
    }

    if (!$viewName)
    {
      if (($actionInstance->getRequestMethods() & $method) != $method)
      {
        // this action will skip validation/execution for this method
        // get the default view
        $viewName = $actionInstance->getDefaultView();
      }
      else
      {
        // set default validated status
        $validated = true;

        // get the current action validation configuration
        $validationConfig = $moduleName.'/'.sfConfig::get('sf_app_module_validate_dir_name').'/'.$actionName.'.yml';

        // load validation configuration
        // do NOT use require_once
        if (null !== $validateFile = sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$validationConfig, true))
        {
          // create validator manager
          $validatorManager = new sfValidatorManager();
          $validatorManager->initialize($context);

          require($validateFile);

          // process validators
          $validated = $validatorManager->execute();
        }

        // process manual validation
        $validateToRun = 'validate'.ucfirst($actionName);
        $manualValidated = method_exists($actionInstance, $validateToRun) ? $actionInstance->$validateToRun() : $actionInstance->validate();

        // action is validated if:
        // - all validation methods (manual and automatic) return true
        // - or automatic validation returns false but errors have been 'removed' by manual validation
        $validated = ($manualValidated && $validated) || ($manualValidated && !$validated && !$context->getRequest()->hasErrors());

        // register fill-in filter
        if (null !== ($parameters = $context->getRequest()->getAttribute('fillin', null, 'symfony/filter')))
        {
          $this->registerFillInFilter($filterChain, $parameters);
        }

        if ($validated)
        {
          if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
          {
            $timer = sfTimerManager::getTimer(sprintf('Action "%s/%s"', $moduleName, $actionName));
          }

          // execute the action
          $statsdTimer = new sfTimer();
          $actionInstance->preExecute();
          c2cActions::statsdTiming('execution.action.preExecute', $statsdTimer->getElapsedTime(), $statsdPrefix);

          $statsdTimer = new sfTimer();
          $viewName = $actionInstance->execute();
          c2cActions::statsdTiming('execution.action.execute', $statsdTimer->getElapsedTime(), $statsdPrefix);

          if ($viewName == '')
          {
            $viewName = sfView::SUCCESS;
          }

          $statsdTimer = new sfTimer();
          $actionInstance->postExecute();
          c2cActions::statsdTiming('execution.action.postExecute', $statsdTimer->getElapsedTime(), $statsdPrefix);

          if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
          {
            $timer->addTime();
          }
        }
        else
        {
          if (sfConfig::get('sf_logging_enabled'))
          {
            $this->context->getLogger()->info('{sfFilter} action validation failed');
          }

          // validation failed
          $handleErrorToRun = 'handleError'.ucfirst($actionName);
          $viewName = method_exists($actionInstance, $handleErrorToRun) ? $actionInstance->$handleErrorToRun() : $actionInstance->handleError();
          if ($viewName == '')
          {
            $viewName = sfView::ERROR;
          }
        }
      }
    }

    if ($viewName == sfView::HEADER_ONLY)
    {
      $context->getResponse()->setHeaderOnly(true);

      // execute next filter
      $filterChain->execute();
    }
    else if ($viewName != sfView::NONE)
    {
      if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
      {
        $timer = sfTimerManager::getTimer(sprintf('View "%s" for "%s/%s"', $viewName, $moduleName, $actionName));
      }

      // get the view instance
      $statsdTimer = new sfTimer();
      $viewInstance = $controller->getView($moduleName, $actionName, $viewName);
      c2cActions::statsdTiming("execution.view.$viewName.getView", $statsdTimer->getElapsedTime(), $statsdPrefix);

      $statsdTimer = new sfTimer();
      $viewInstance->initialize($context, $moduleName, $actionName, $viewName);
      c2cActions::statsdTiming("execution.view.$viewName.initialize", $statsdTimer->getElapsedTime(), $statsdPrefix);

      $statsdTimer = new sfTimer();
      $viewInstance->execute();
      c2cActions::statsdTiming("execution.view.$viewName.execute", $statsdTimer->getElapsedTime(), $statsdPrefix);

      // render the view and if data is returned, stick it in the
      // action entry which was retrieved from the execution chain
      $statsdTimer = new sfTimer();
      $viewData = $viewInstance->render();
      c2cActions::statsdTiming("execution.view.$viewName.render", $statsdTimer->getElapsedTime(), $statsdPrefix);

      if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
      {
        $timer->addTime();
      }

      if ($controller->getRenderMode() == sfView::RENDER_VAR)
      {
        $actionEntry->setPresentation($viewData);
      }
      else
      {
        // execute next filter
        $filterChain->execute();
      }
    }
  }
}
