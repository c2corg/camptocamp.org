<?php

/*
 * This file comes the symfony package (originally sfCacheFilter.class.php) and has been reworked by me (vdb).
 */

class MyCacheFilter extends sfCacheFilter
{
  protected
    $cacheManager = null,
    $request      = null,
    $response     = null,
    $cache        = array(),
    $interface_language = null, // added
    $credentials  = null; // added

  /**
   * Initializes this Filter.
   *
   * @param sfContext The current application context
   * @param array   An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Filter
   */
  public function initialize($context, $parameters = array())
  {
    parent::initialize($context, $parameters);

    $this->cacheManager = $context->getViewCacheManager();
    $this->request      = $context->getRequest();
    $this->response     = $context->getResponse();
    
    $user = $this->getContext()->getUser(); 
    // replaced $context by getcontext to get latest available context, so that cache presents true user connected status.
    $this->interface_language = $user->getCulture(); 
    $this->credentials = (int)$user->isConnected() + (int)$user->hasCredential('moderator'); 
    // anonymous : 0
    // connected : 1
    // moderator : 2
  }

    public static function getCurrentCacheUri()
    {
        // for home, portals filter and default list, we adapt cache uri so that we can cache
        // cases where all cultures are displayed, or only the one from the interface (see #723)
        $context = sfContext::getInstance();
        $module = $context->getModuleName();
        $action = $context->getActionName();
        
        $request_parameters = $context->getRequest()->getParameterHolder()->getAll();
        unset($request_parameters['module']);
        unset($request_parameters['action']);
        
        $is_cacheable_filter_list = false;
        if (in_array($action, array('filter', 'list')))
        {
            $count_request_parameters = count($request_parameters);
                
            switch($action)
            {
                case 'list':
                    if (!isset($request_parameters['page']))
                    {
                        $request_parameters['page'] = 1;
                        $count_request_parameters++;
                    }
                    if (    $request_parameters['page'] <= 2
                         && (   $count_request_parameters == 1
                             || (   $module == 'outings'
                                 && $count_request_parameters == 3
                                 && isset($request_parameters['orderby'])
                                 && $request_parameters['orderby'] == 'date'
                                 && isset($request_parameters['order'])
                                 && $request_parameters['order'] == 'desc'
                                )
                            )
                       )
                    {
                        $is_cacheable_filter_list = true;
                    }
                    break;
                
                case 'filter';
                    if (!count($request_parameters))
                    {
                        $is_cacheable_filter_list = true;
                    }
                    break;
                
                default:
                    break;
            }
        }
        
        $uri = sfRouting::getInstance()->getCurrentInternalUri();
        $il = 'il=' . $context->getUser()->getCulture();
        $pl = '';
        $pa = '';
        $credential = ((int)$context->getUser()->isConnected() + (int)$context->getUser()->hasCredential('moderator'));
        $c = '&c=' . $credential;
        
        if ($action == 'home' || $module == 'portals' && $action == 'view' || $is_cacheable_filter_list)
        {
            if ($action == 'home' || $module == 'portals' && $action == 'view')
            {
                $module_cache = 'outings';
            }
            else
            {
                $module_cache = $module;
            }
            $perso = c2cPersonalization::getInstance();
            list($langs_enable, $areas_enable, $activities_enable) = c2cPersonalization::getDefaultFilters($module_cache);
            $is_main_filter_switch_on = $perso->isMainFilterSwitchOn();
            $activities_filter = $perso->getActivitiesFilter();
            
            if ($is_main_filter_switch_on)
            {
                if (   $action != 'filter'
                    && (   $action != 'list'
                        || $langs_enable
                       )
                    && $perso->areDefaultLanguagesFilters()
                   )
                {
                    $pl = '&pl=1';
                }
                if ($activities_enable && count($activities_filter))
                {
                    $pa = '&pa=' . implode('-', $activities_filter);
                }
            }
            
            switch ($action)
            {
                case 'view' :
                    if (!in_array($module, array('outings', 'images', 'articles', 'users')) && isset($request_parameters['version']))
                    {
                        $c = '';
                    }
                    break;
                
                case 'history' :
                    if (!in_array($module, array('outings', 'images', 'articles', 'users')))
                    {
                        $c = '';
                    }
                case 'whatsnew' :
                    $c = '';
                    break;
                
                case 'list' :
                    if (strpos($uri, 'page=') === false)
                    {
                        $uri .= ((strpos($uri, '?')) ? '&' : '?')
                              . 'page=1';
                    }
                    if ($module == 'users')
                    {
                        $c = '&c=' . min($credential, 1);
                    }
                    else
                    {
                        $c = '';
                    }
                    break;
                
                default:
                    break;
            }
        }

        $uri .= ((strstr($uri, '?')) ? '&' : '?')
              . $il . $pl . $pa . $c;
        
        return $uri;
    }

  public function executeBeforeExecution()
  {
    $context = $this->getContext();
    $module = $context->getModuleName();
    $action = $context->getActionName();
    
    $request_parameters = $context->getRequest()->getParameterHolder()->getAll();
    unset($request_parameters['module']);
    unset($request_parameters['action']);
    $count_request_parameters = count($request_parameters);

    // register our cache configuration
    $this->cacheManager->registerConfiguration($module);
    // this previous line removes the dynamic cache configuration we could have been adding in previous filter !
    // so that conditionalCacheFilter defined in documentation :
    // http://www.symfony-project.com/book/1_0/12-Caching#Configuring%20the%20Cache%20Dynamically
    // does not work !

    if ($action == 'home' || $module == 'portals' && $action == 'view')
    {
        $module_cache = 'outings';
    }
    else
    {
        $module_cache = $module;
    }
    $perso = c2cPersonalization::getInstance();
    list($langs_enable, $areas_enable, $activities_enable) = c2cPersonalization::getDefaultFilters($module_cache);
    $are_filters_active = $perso->areFiltersActive();
    $is_main_filter_switch_on = $perso->isMainFilterSwitchOn();
    $count_activities_filter = count($perso->getActivitiesFilter());
    $count_places_filter = count($perso->getPlacesFilter());
    $are_default_filters = $perso->areDefaultFilters();
    $are_simple_activities_langs_filters = $perso->areSimpleActivitiesFilters(true);
    $are_simple_activities_filters = $perso->areSimpleActivitiesFilters(false);
   
    // portals and home cache
    // Following condition means that filter is deactivated, or filters are empty,
    // or there is only one culture in the prefs, which the same as the interface culture 
    // Other cases should not happen often, we don't cache them
    switch($action)
    {
        case 'home':
        case 'view':
            if (    !$are_filters_active
                 || !$is_main_filter_switch_on
                 || $are_default_filters
                 || $are_simple_activities_langs_filters
               )
            {
                $this->cacheManager->addCache('documents', 'home', array('lifeTime' => 300, 'vary' => array()));
                $this->cacheManager->addCache('portals', 'changerdapproche', array('lifeTime' => 600, 'vary' => array()));
                $this->cacheManager->addCache('portals', 'view', array('lifeTime' => 600, 'vary' => array()));
            }
            break;
    
        case 'list':
            if (!isset($request_parameters['page']))
            {
                $request_parameters['page'] = 1;
                $count_request_parameters++;
            }
            if (    $request_parameters['page'] <= 2
                 && (   $count_request_parameters == 1
                     || (   $module == 'outings'
                         && $count_request_parameters == 3
                         && isset($request_parameters['orderby'])
                         && $request_parameters['orderby'] == 'date'
                         && isset($request_parameters['order'])
                         && $request_parameters['order'] == 'desc'
                        )
                    )
                 && (   !$are_filters_active
                     || !$is_main_filter_switch_on
                     || $perso->areCacheableFilters($module)
                    )
               )
            {
                $this->cacheManager->addCache($module, 'list', array('lifeTime' => 350000, 'vary' => array()));
            }
            break;
    
        case 'filter':
            if (    !$count_request_parameters
                 && !$count_places_filter
                 && (   !$are_filters_active
                     || !$is_main_filter_switch_on
                     || $are_simple_activities_filters
                    )
               )
            {
                if (in_array($module, array('outings', 'images')))
                {
                    $lifetime = 86400;
                }
                else
                {
                    $lifetime = 600000;
                }
                $this->cacheManager->addCache($module, 'filter', array('lifeTime' => $lifetime, 'vary' => array()));
            }
            break;
    
        case 'cdasearch':
            if (    !$count_request_parameters
                 || !$count_places_filter
               )
            {
                $this->cacheManager->addCache($module, 'cdasearch', array('lifeTime' => 350000, 'vary' => array()));
            }
            break;
        
        default:
            break;
    }
    
    if (!$is_main_filter_switch_on || !$count_activities_filter)
    {
        $this->cacheManager->addCache('common', '_menu', array('lifeTime' => 350000, 'vary' => array()));
    }

    // get current uri adapted for cache
    $uri = self::getCurrentCacheUri();
    
    // page cache
    $this->cache[$uri] = array('page' => false, 'action' => false);
    $cacheable = $this->cacheManager->isCacheable($uri);
    if ($cacheable)
    {
      if ($this->cacheManager->withLayout($uri))
      {
        $inCache = $this->getPageCache($uri);
        $this->cache[$uri]['page'] = !$inCache;

        if ($inCache)
        {
          // page is in cache, so no need to run execution filter
          return false;
        }
      }
      else
      {
        $inCache = $this->getActionCache($uri);
        $this->cache[$uri]['action'] = !$inCache;
      }
    }

    return true;
  }

  /**
   * Executes this filter.
   *
   * @param sfFilterChain A sfFilterChain instance.
   */
  public function executeBeforeRendering()
  {
    // cache only 200 HTTP status
    if (200 != $this->response->getStatusCode())
    {
      return;
    }

    // get current uri adapted for cache
    $uri = self::getCurrentCacheUri();

    // save page in cache
    if ($this->cache[$uri]['page'])
    {
      // set some headers that deals with cache
      $lifetime = $this->cacheManager->getClientLifeTime($uri, 'page');
      $this->response->setHttpHeader('Last-Modified', $this->response->getDate(time()), false);
      $this->response->setHttpHeader('Expires', $this->response->getDate(time() + $lifetime), false);
      $this->response->addCacheControlHttpHeader('max-age', $lifetime);

      // set Vary headers
      foreach ($this->cacheManager->getVary($uri, 'page') as $vary)
      {
        $this->response->addVaryHttpHeader($vary);
      }

      $this->setPageCache($uri);
    }
    else if ($this->cache[$uri]['action'])
    {
      // save action in cache
      $this->setActionCache($uri);
    }

    // remove PHP automatic Cache-Control and Expires headers if not overwritten by application or cache
    if (strpos($this->getContext()->getRequest()->getScriptName(),'forums') === false &&
                    (sfConfig::get('sf_etag') || $this->response->hasHttpHeader('Last-Modified')))
    {
      // FIXME: All pages of forum with "index" in name, have some prob. with the cache (etag ?)
      // FIXME: these headers are set by PHP sessions (see session_cache_limiter())
      $this->response->setHttpHeader('Cache-Control', null, false);
      $this->response->setHttpHeader('Expires', null, false);
      $this->response->setHttpHeader('Pragma', null, false);
    }

    // Etag support
    if (sfConfig::get('sf_etag'))
    {
      $etag = md5($this->response->getContent());
      $this->response->setHttpHeader('ETag', $etag);

      if ($this->request->getHttpHeader('IF_NONE_MATCH') == $etag)
      {
        $this->response->setStatusCode(304);
        $this->response->setHeaderOnly(true);

        if (sfConfig::get('sf_logging_enabled'))
        {
          $this->getContext()->getLogger()->info('{sfFilter} ETag matches If-None-Match (send 304)');
        }
      }
    }

    // conditional GET support
    // never in debug mode
    if ($this->response->hasHttpHeader('Last-Modified') && !sfConfig::get('sf_debug'))
    {
      $last_modified = $this->response->getHttpHeader('Last-Modified');
      $last_modified = $last_modified[0];
      if ($this->request->getHttpHeader('IF_MODIFIED_SINCE') == $last_modified)
      {
        $this->response->setStatusCode(304);
        $this->response->setHeaderOnly(true);

        if (sfConfig::get('sf_logging_enabled'))
        {
          $this->getContext()->getLogger()->info('{sfFilter} Last-Modified matches If-Modified-Since (send 304)');
        }
      }
    }
  }
}
