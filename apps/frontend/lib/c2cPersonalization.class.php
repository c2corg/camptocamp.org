<?php

class c2cPersonalization
{

    public static function getLanguagesFilter()
    {
        $langs = self::getFilterParameters(sfConfig::get('app_personalization_cookie_languages_name'));
        $ranges = self::getPlacesFilter();
        $culture = sfContext::getInstance()->getUser()->getCulture();
        if (!$langs && !$ranges && $culture != 'en')
        {
            $langs[] = $culture;
        }
        return  $langs;
    }

    public static function getPlacesFilter()
    {
        return self::getFilterParameters(sfConfig::get('app_personalization_cookie_places_name'));
    }

    public static function getActivitiesFilter()
    {
        return self::getFilterParameters(sfConfig::get('app_personalization_cookie_activities_name'));
    }

    public static function getFilterParameters($personal_filter_name)
    {
        $instance = sfContext::getInstance();
        $cookie = $instance->getRequest()->getCookie($personal_filter_name);

        if(!is_null($cookie))
        {
            $parameters = explode(',', urldecode($cookie));
        }
        else
        {
            $parameters = array();
        }

        return $parameters;
    }

    public static function saveFilter($personal_filter_name, $parameters = null)
    {
        $response = sfContext::getInstance()->getResponse();

        if(is_null($parameters))
        {
            // parameters empty, we erase cookie
            $response->setCookie($personal_filter_name, '');
        }
        else
        {
            $response->setCookie($personal_filter_name,
                             urlencode(implode(',', $parameters)),
                             time() + sfConfig::get('app_personalization_filter_timeout'));
        }
    }

    /**
     * Tells if user has some filters activated.
     * @return boolean
     */
    public static function areFiltersActive()
    {
        return self::getLanguagesFilter() || 
               self::getPlacesFilter() ||
               self::getActivitiesFilter();
    }
    
    /**
     * Sets the FilterSwitch cookie to ON or OFF
     * @return boolean
     */
    public static function setFilterSwitch($on = true)
    {
        $response = sfContext::getInstance()->getResponse();
        $status = ($on) ? 'true' : 'false';
        $response->setCookie(sfConfig::get('app_personalization_cookie_switch_name'),
                             $status,
                             time() + sfConfig::get('app_personalization_filter_timeout'));
    }
    
    /**
     * Tells us if FilterSwitch cookie has been set to true.
     * @return boolean
     */
    public static function getFilterSwitch()
    {
        $request = sfContext::getInstance()->getRequest();
        $cookie_name = sfConfig::get('app_personalization_cookie_switch_name');
        if ($request->getCookie($cookie_name) == 'true')
        {
            c2cTools::log('request has cookie switch and its value is: '.$request->getCookie($cookie_name));
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Tells us if main filter is activated (taking into account attribute and cookie, plus the priority: attribute > cookie).
     * This is the method to use to determine whether the main FilterSwitch is ON or OFF (and not getFilterSwitch).
     *
     * @return boolean
     */
    public static function isMainFilterSwitchOn()
    {    
        $instance = sfContext::getInstance();
        $user = $instance->getUser();
        
        if ($user->hasAttribute('filters_switch'))
        {
            if ($user->getAttribute('filters_switch') == true)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        elseif (c2cPersonalization::getFilterSwitch())
        {
            return true;
        }
        else
        {
            $langs = self::getLanguagesFilter();
            $cookie_name = sfConfig::get('app_personalization_cookie_switch_name');
            $cookie = $instance->getRequest()->getCookie($cookie_name);

            if(is_null($cookie) && $langs)
            {
                $user->setFiltersSwitch(true);
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}
