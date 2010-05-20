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

  public function executeBeforeExecution()
  {
    // register our cache configuration
    $this->cacheManager->registerConfiguration($this->getContext()->getModuleName());
    // this previous line removes the dynamic cache configuration we could have been adding in previous filter !
    // so that conditionalCacheFilter defined in documentation :
    // http://www.symfony-project.com/book/1_0/12-Caching#Configuring%20the%20Cache%20Dynamically
    // does not work !

    $perso = c2cPersonalization::getInstance();
    $is_main_filter_switch_on = $perso->isMainFilterSwitchOn();
    
    if (!$perso->areFiltersActive() || !$is_main_filter_switch_on || $perso->areDefaultFilters())
    {
        $this->cacheManager->addCache('documents', 'home', array('lifeTime' => 300, 'vary' => array()));
        $this->cacheManager->addCache('portals', 'changerdapproche', array('lifeTime' => 600, 'vary' => array()));
        $this->cacheManager->addCache('portals', 'view', array('lifeTime' => 600, 'vary' => array()));
    }
    
    if (!$is_main_filter_switch_on || count($perso->getActivitiesFilter()) == 0)
    {
        $this->cacheManager->addCache('common', 'menu', array('lifeTime' => 84600, 'vary' => array()));
    }

    $uri = sfRouting::getInstance()->getCurrentInternalUri();
    $uri .= (strstr($uri, '?')) ? '&' : '?'; // added
    $uri .= 'il='.$this->interface_language.'&c='.$this->credentials; // added

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

    $uri = sfRouting::getInstance()->getCurrentInternalUri();
    $uri .= (strstr($uri, '?')) ? '&' : '?'; // added
    $uri .= 'il='.$this->interface_language.'&c='.$this->credentials; // added

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
