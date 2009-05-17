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

                if (!$context->getRequest()->getCookie('symfony') && $culture != 'en')
                {
                    // TODO: get symfony cookie name from config (factories.yml)?
            
                    // apparently he comes on the site for the first time
                    // => set his language filter to the interface language by default (except for EN
                    // because a lot of browsers are configured in english)
                    $langs[] = $culture;
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
        $response = sfContext::getInstance()->getResponse();

        $parameters_flat = is_null($parameters) ?
                           '' :
                           urlencode(is_array($parameters) ? implode(',', $parameters) : $parameters);

        // save filter in profile if user connected (== user_id not null)
        if ($user_id != null)
        {
                self::savePrefCookie($user_id, $filter_name, $parameters_flat);
        }

        // save filter as cookie
        if ($save_cookie)
        {
            if (is_null($parameters))
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

    protected static function savePrefCookie($user_id, $cookie_name, $cookie_value) // todo todo todo plusieurs a la fois ? forums ?? SECURITY : limit text size
    {
        if (!$user_private_data = UserPrivateData::find($user_id)) // logged user db object
        {
            $this->setNotFoundAndRedirect();// TODO just siulently stop???...ou faire un message comme quand je sai splus quoi en ajax...??
        }

        $conn = sfDoctrine::Connection();
        try
        {
            $cookie_prefs = $user_private_data->getPref_cookies();
            $cookie_prefs[$cookie_name] = $cookie_value;
            // TODO Que faire si entree vide ??????
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
        $managed_cookies = sfConfig::get('mod_users_profile_cookies_list');

        // erase all managed cookies
        foreach ($managed_cookies as $cookie)
        {
            $response->setCookie($cookie, '');
        }
        // remove specific cookie for transition, can be safely removed from netx upgrade
        $response->setCookie('punbb_dyncat', '', null, '/forums/');

        $cookie_prefs = $user_private_data->getPref_cookies();
        if (empty($cookie_prefs))
        {
            return;
        }
        foreach ($cookie_prefs as $cookie_name => $cookie_value)
        {
            sfContext::getInstance()
                ->getResponse()
                ->setCookie($cookie_name, $cookie_value, 
                            time() + sfConfig::get('app_personalization_filter_timeout'));
        }
    }
}
