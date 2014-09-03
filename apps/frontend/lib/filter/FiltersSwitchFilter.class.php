<?php

/**
 * $Id$
 */
class FiltersSwitchFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = $this->getContext();
        $session_user = $context->getUser();
        
        // we are looking at a potential request for switching main filter:
        if ($session_user->hasAttribute('filters_switch'))
        {
            $cookie_value = $context->getRequest()->getCookie(sfConfig::get('app_personalization_cookie_switch_name'));

            if ($cookie_value)
            {
                $cookie_value = ($cookie_value == 'true') ? true : false ;

                if ($session_user->getAttribute('filters_switch') == $cookie_value)
                {
                    // if cookie has been set, and both values match, remove attribute in user session.
                    $session_user->getAttributeHolder()->remove('filters_switch');
                    //c2cTools::log("{FiltersSwitchFilter} we're now removing attribute in user session because cookie has been set and cookie value=attribute value");
                }
                else
                {
                    // set cookie to attribute value.
                    $attr_value = $session_user->getAttribute('filters_switch');
                    c2cPersonalization::setFilterSwitch($attr_value, $session_user->isConnected() ? $session_user->getId() : null);
                    //c2cTools::log("{FiltersSwitchFilter} we're now setting cookie value to $attr_value because cookie value != attribute value and attribute is stronger than cookie");
                }
            }
            else
            {
                // there's not yet a cookie, but it has been requested to write it.
                $new_status = (int)$session_user->getAttribute('filters_switch');
                //c2cTools::log("{FiltersSwitchFilter} No cookie, but attribute present => we're now asking to set main filter switch to $new_status");
                c2cPersonalization::setFilterSwitch($new_status, $session_user->isConnected() ? $session_user->getId() : null);
            }
        }

        $filterChain->execute();
    }
}
