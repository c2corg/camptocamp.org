<?php

class c2cPersonalization
{
    protected
        $languagesFilter = null,
        $placesFilter = null,
        $activitesFilter = null,
        $isFilterSwitchOn = null,
        $areFiltersSet = null;

    protected static $instance = null;

    /**
     * Retrieve the singleton instance of this class.
     *
     * @return c2cPersonalization A c2cPersonalization implementation instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    public function getLanguagesFilter()
    {
        if (!isset($this->languagesFilter))
        {
            $cookie_name = sfConfig::get('app_personalization_cookie_languages_name');
            $langs = self::getFilterParameters($cookie_name);
            $context = sfContext::getInstance();
            $culture = $context->getUser()->getCulture();

            $nb_langs = count($langs);
            if ($nb_langs == 0)
            {
                // user has no language filter

                if (!$context->getRequest()->getCookie('symfony') && !in_array($culture, array('fr', 'en', 'de', 'eu')))
                {
                    // TODO: get symfony cookie name from config (factories.yml)?
            
                    // apparently he comes on the site for the first time
                    // => set his language filter to the interface language by default for IT, CA and ES
                    if (in_array($culture, array('es', 'ca')))
                    {
                        $langs = array('es', 'ca', 'en');
                    }
                    else
                    {
                        $langs = array($culture);
                    }
                    
                    self::saveFilter($cookie_name, $langs);
                }
            }
            elseif ($nb_langs == count(sfConfig::get('app_languages_c2c')))
            {
                // deactivate filter if all languages are selected
                $langs = array();
                self::saveFilter($cookie_name, $langs);
            }

            $this->languagesFilter = $langs;
        }
        
        return  $this->languagesFilter;
    }

    public function getPlacesFilter()
    {
        if (!isset($this->placesFilter))
        {
            $this->placesFilter = self::getFilterParameters(sfConfig::get('app_personalization_cookie_places_name'));
        }

        return $this->placesFilter;
    }

    public function getActivitiesFilter()
    {
        if (!isset($this->activitiesFilter))
        {
            $cookie_name = sfConfig::get('app_personalization_cookie_activities_name');
            $this->activitiesFilter = self::getFilterParameters($cookie_name);

            // deactivate filter if all activities are selected
            if (count($this->activitiesFilter) == count(sfConfig::get('app_activities_list')) - 1)
            {
                $this->activitiesFilter = array();
                self::saveFilter($cookie_name, $this->activitiesFilter);
            }
        }

        return $this->activitiesFilter;
    }

    protected static function getFilterParameters($personal_filter_name)
    {
        $cookie = sfContext::getInstance()->getRequest()->getCookie($personal_filter_name);
        $parameters = !is_null($cookie) ? explode(',', urldecode($cookie)) : array();
        return $parameters;
    }

    public static function saveFilter($filter_name, $parameters = null, $user_id = null, $save_cookie = true)
    {
        self::saveFilters(array($filter_name => $parameters), $user_id, $save_cookie);
    }

    public static function saveFilters($filters, $user_id = null, $save_cookie = true)
    {
        $response = sfContext::getInstance()->getResponse();

        $filters_flat = array();
        foreach ($filters as $filter_name => $parameters)
        {
            $filters_flat[$filter_name] = is_null($parameters) ?
                           '' :
                           urlencode(is_array($parameters) ? implode(',', $parameters) : $parameters);
        }

        // save filters in profile if user connected (== user_id not null)
        if ($user_id != null)
        {
            self::savePrefCookies($user_id, $filters_flat);
        }

        // save filters as cookie
        // rq no need to worry about fold, since it is called via ajax and thus cookie is saved in js
        if ($save_cookie)
        {
            foreach ($filters_flat as $filter_name => $parameters_flat)
            {
                if (empty($parameters_flat))
                {
                    // parameters empty, we erase cookie
                    $response->setCookie($filter_name, '');
                }
                else
                {
                    $response->setCookie($filter_name, $parameters_flat,
                                         time() + sfConfig::get('app_personalization_filter_timeout'));
                }
            }
        }
    }

    /**
     * Tells if user has filters which allow le $modul list to be cahed
     * @return boolean
     */
    public static function getDefaultFilters($module)
    {
        switch ($module)
        {
            case 'outings' :
                $default_filters = array(true, true, true);
                break;
            case 'images' :
                $default_filters = array(false, false, true);
                break;
            case 'routes' :
                $default_filters = array(false, true, true);
                break;
            case 'summits' :
                $default_filters = array(false, true, false);
                break;
            case 'sites' :
                $default_filters = array(false, true, false);
                break;
            case 'articles' :
                $default_filters = array(true, false, true);
                break;
            case 'parkings' :
                $default_filters = array(false, true, false);
                break;
            case 'huts' :
                $default_filters = array(false, true, false);
                break;
            case 'books' :
                $default_filters = array(false, false, true);
                break;
            case 'users' :
                $default_filters = array(false, true, true);
                break;
            case 'products' :
                $default_filters = array(false, true, false);
                break;
            case 'portals' :
                //$default_filters = array(false, true, false);
                $default_filters = array(false, false, false);
                break;
            case 'areas' :
                $default_filters = array(false, false, false);
                break;
            case 'maps' :
                $default_filters = array(false, true, false);
                break;
            case 'documents' :
                $default_filters = array(false, false, false);
                break;
            default :
                $default_filters = array(false, false, false);
                break;
        }
        
        return $default_filters;
    }

    public static function getDefaultFiltersUrlParam($module, $prefix = array())
    {
        list($langs_enable, $areas_enable, $activities_enable) = self::getDefaultFilters($module);
        
        $param = array();
        if ($langs_enable)
        {
            $param[] = 'cult';
        }
        if ($areas_enable)
        {
            $param[] = 'areas';
        }
        if ($activities_enable)
        {
            $param[] = 'act';
        }
        
        if (count($param))
        {
            $param = array_merge($prefix, $param);
        }
        
        $param = implode('-', $param);
        
        return $param;
    }
    
    /**
     * Tells if user has some filters activated.
     * @return boolean
     */
    public function areFiltersActive()
    {
        if (!isset($this->areFiltersSet))
        {
            $this->areFiltersSet = (bool)$this->getLanguagesFilter() || 
                                   (bool)$this->getPlacesFilter() ||
                                   (bool)$this->getActivitiesFilter();
        }
        return $this->areFiltersSet;
    }

    /**
     * Tells if user has default filters activated (just lang filter, with only 1 lang = interface lang)
     * @return boolean
     */
    public function areDefaultFilters()
    {
        $langs      = $this->getLanguagesFilter();
        $areas      = $this->getPlacesFilter();
        $activities = $this->getActivitiesFilter();
        $context = sfContext::getInstance();
        $culture = $context->getUser()->getCulture();
        if (count($langs) == 1 && count($areas) == 0 && count($activities) == 0)
        {
            $is_default_filter = (reset($langs) == $culture);
        }
        else
        {
            $is_default_filter = false;
        }
        return $is_default_filter;
    }

    /**
     * Tells if user has default language filters activated ( lang filter, with only 1 lang = interface lang)
     * @return boolean
     */
    public function areDefaultLanguagesFilters($check_areas = true)
    {
        $langs = $this->getLanguagesFilter();
        if ($check_areas)
        {
            $areas = $this->getPlacesFilter();
        }
        else
        {
            $areas = array();
        }
        $context = sfContext::getInstance();
        $culture = $context->getUser()->getCulture();
        if (count($langs) == 1 && count($areas) == 0)
        {
            $is_default_filter = (reset($langs) == $culture);
        }
        else
        {
            $is_default_filter = false;
        }
        return $is_default_filter;
    }

    /**
     * Tells if user has simple activity filters activated (just 1 or 2 activities filter + lang filter, with only 0 or 1 lang = interface lang)
     * @return boolean
     */
    public function areSimpleActivitiesFilters($check_langs = true)
    {
        if ($check_langs)
        {
            $langs = $this->getLanguagesFilter();
        }
        else
        {
            $langs = array();
        }
        $areas = $this->getPlacesFilter();
        $count_activities = count($this->getActivitiesFilter());
        $context = sfContext::getInstance();
        $culture = $context->getUser()->getCulture();
        if (count($langs) <= 1 && count($areas) == 0 && ($count_activities == 1 || $count_activities == 2))
        {
            if (count($langs) == 1)
            {
                $is_default_filter = (reset($langs) == $culture);
            }
            else
            {
                $is_default_filter = true;
            }
        }
        else
        {
            $is_default_filter = false;
        }
        return $is_default_filter;
    }

    /**
     * Tells if user has filters which allow le $modul list to be cahed
     * @return boolean
     */
    public function areCacheableFilters($module)
    {
        $langs      = $this->getLanguagesFilter();
        $context = sfContext::getInstance();
        $culture = $context->getUser()->getCulture();
        if (count($langs) == 1)
        {
            $langs_cacheable = (reset($langs) == $culture);
        }
        else
        {
            $langs_cacheable = true;
        }
        
        $areas      = $this->getPlacesFilter();
        $areas_cacheable = (count($areas) == 0);
        
        $activities = $this->getActivitiesFilter();
        $activities_cacheable = (count($activities) <= 2);
        
        list($langs_enable, $areas_enable, $activities_enable) = $this->getDefaultFilters($module);
        
        $is_cacheable =    (!$langs_enable || $langs_cacheable)
                        && (!$areas_enable || $areas_cacheable)
                        && (!$activities_enable || $activities_cacheable);
        
        return $is_cacheable;
    }

    /**
     * Tells if user has some filters activated and if main filter is activated.
     * @return boolean
     */
    public function areFiltersActiveAndOn($module)
    {
        list($langs, $areas, $activities) = $this->getDefaultFilters($module);
        
        return      $this->isMainFilterSwitchOn()
                && (
                        ($langs && (bool)$this->getLanguagesFilter())
                    ||  ($areas && (bool)$this->getPlacesFilter())
                    ||  ($activities && (bool)$this->getActivitiesFilter())
                   );
    }
    
    /**
     * Sets the FilterSwitch cookie to ON or OFF
     * @return boolean
     */
    public static function setFilterSwitch($on = true, $user_id = null)
    {
        $response = sfContext::getInstance()->getResponse();
        self::saveFilter(sfConfig::get('app_personalization_cookie_switch_name'), $on ? 'true' : 'false', $user_id);
    }
    
    public static function getFilterSwitch()
    {
        $request = sfContext::getInstance()->getRequest();
        $cookie_name = sfConfig::get('app_personalization_cookie_switch_name');
        return $request->getCookie($cookie_name);
    }
    
    /**
     * Tells us if main filter is activated (taking into account attribute and cookie, plus the priority: attribute > cookie).
     * This is the method to use to determine whether the main FilterSwitch is ON or OFF (and not getFilterSwitch).
     *
     * @return boolean
     */
    public function isMainFilterSwitchOn()
    {
        if (!isset($this->isFilterSwitchOn))
        {
            $this->isFilterSwitchOn = $this->detectIfFilterSwitchIsOn();
        }

        return $this->isFilterSwitchOn;
    }

    protected function detectIfFilterSwitchIsOn()
    {
        $context = sfContext::getInstance();
        $user = $context->getUser();
    
        if ($user->hasAttribute('filters_switch'))
        {
            return $user->getAttribute('filters_switch');
        }

        $cookie = self::getFilterSwitch();
        if ($cookie == 'true') return true;
        if ($cookie == 'false') return false;

        $langs = $this->getLanguagesFilter();
        if (!empty($langs))
        {
            $user->setFiltersSwitch(true);
            return true;
        }

        return false;
    }

    protected static function savePrefCookie($user_id, $cookie_name, $cookie_value)
    {
        self::savePrefCookies($user_id, array($cookie_name => $cookie_value));
    }

    protected static function savePrefCookies($user_id, $cookies)
    {
        if (!$user_private_data = UserPrivateData::find($user_id)) // logged user db object
        {
            $this->setNotFoundAndRedirect();
        }

        $conn = sfDoctrine::Connection();
        try
        {
            $cookie_prefs = $user_private_data->getPref_cookies();
            foreach ($cookies as $cookie_name => $cookie_value)
            {
                $cookie_prefs[$cookie_name] = $cookie_value;
            }
            $user_private_data->setPref_cookies($cookie_prefs);
            $user_private_data->save();
            $conn->commit();
        }
        catch (Exception $e)
        {
            $conn->rollback();
        }
    }

    /**
     * restore cookie values from profile. Managed cookies not in the profile will be deleted
     */
    public static function restorePrefCookies($user_id)
    {
        if (!$user_private_data = UserPrivateData::find($user_id))
        {
            return; // silently stop
        }

        $response = sfContext::getInstance()->getResponse();
        $managed_cookies = sfConfig::get('app_profile_cookies_list');
        $fold_prefs = sfConfig::get('app_personalization_cookie_fold_positions');

        $cookie_prefs = $user_private_data->getPref_cookies();

        if (empty($cookie_prefs))
        {
            // no saved value in profile, copy the current cookie values into profile

            // 'regular' cookies
            $cookie_values = array();
            foreach ($managed_cookies as $cookie)
            {
                if (sfContext::getInstance()->getRequest()->getCookie($cookie))
                {
                    $cookie_values[$cookie] = urlencode(sfContext::getInstance()->getRequest()->getCookie($cookie));
                }
            }
            // fold prefs
            if (sfContext::getInstance()->getRequest()->getCookie('fold'))
            {
                $fold_cookie_value = sfContext::getInstance()->getRequest()->getCookie('fold');
                foreach ($fold_prefs as $pos => $pref)
                {
                    
                    if ($fold_cookie_value[$pos] == 't')
                    {
                        $cookie_values[$pref+'_home_status'] = 'true';
                    }
                    else if ($fold_cookie_value[$pos] == 'f')
                    {
                        $cookie_values[$pref+'_home_status'] = 'false';
                    }
                }
            }
            if (!empty($cookie_values))
            {
                $conn = sfDoctrine::Connection();
                try
                {
                     $user_private_data->setPref_cookies($cookie_values);
                     $user_private_data->save();
                     $conn->commit();
                }
                catch (Exception $e)
                {
                    $conn->rollback();
                }
            }
        }
        else
        {
            // set fold cookie
            $fold_cookie_value = $default = str_repeat('x', sfConfig::get('app_personalization_cookie_fold_size'));
            foreach ($fold_prefs as $pos => $pref)
            {
                if (isset($cookie_prefs[$pref.'_home_status']))
                {
                    $fold_cookie_value[$pos] = ($cookie_prefs[$pref.'_home_status'] == 'true') ? 't' : 'f';
                }
            }
            if ($fold_cookie_value != $default)
            {
                $response->setCookie('fold', $fold_cookie_value,
                    time() + sfConfig::get('app_personalization_filter_timeout'));
            }
            else
            {
                $response->setCookie('fold', '');
            }

            // erase all managed cookies or replace values with the one in profile
            foreach ($managed_cookies as $cookie_name)
            {
                if (array_key_exists($cookie_name, $cookie_prefs))
                {
                     $response->setCookie($cookie_name, $cookie_prefs[$cookie_name],
                         time() + sfConfig::get('app_personalization_filter_timeout'));
                }
                else
                {
                    $response->setCookie($cookie_name, '');
                }
            }
        }
    }
}
