<?php

/*
 * This filter is intended to switch between regular and mobile
 * version of the site, based on the hostname
 */
class MobileFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = $this->getContext();
        if (c2cTools::mobileVersion())
        {
            // change layout for mobile_layout
            $context->getResponse()->setParameter($context->getModuleName().'_'.$context->getActionName().'_layout', 'mobile_layout', 'symfony/action/view');
            $context->getResponse()->addStylesheet('/static/css/mobile.css', 'last');
        }
        else
        {
            // TODO the user agent detection can probably be improved...
            // FIXME should we redirect tablets or not? (see also http://android-developers.blogspot.com/2010/12/android-browser-user-agent-issues.html)
            // - Mobile should catch android phones, ipod touch, iphone, ipads (even if we maybe don't want it), windows phone 7 etc
            // - Symbian, Nokia, Blackberry self explainable
            // - SAMSUNG for Bada and other samsung devices
            // - Mini for Opera Mini
            // Maybe we should add more, but it doesn't represent many visits and users can still directly access mobile version of the site
            // if you make changes here, be sure also to check web/forums/include/common.php

            // we check if the user agent is one from a smartphone. If so, and if there is no cookie preventing redirection,
            // we redirect
            if (preg_match('/(Mobile|Symbian|Nokia|SAMSUNG|BlackBerry|Mini|Android)/i', $_SERVER['HTTP_USER_AGENT'])) {
                // we do not redirect if user has a cookie stating that we shouldn't
                if (!$context->getRequest()->getCookie('nomobile'))
                {
                    // if referer is mobile version, it means that the user deliberatly wanted to have the classic version
                    // we thus do not redirect, and add a cookie to prevent further redirections
                    $mobile_host = sfConfig::get('app_mobile_version_host');
                    if (isset($_SERVER['HTTP_REFERER']) &&
                        !empty($mobile_host) &&
                        preg_match('/'.$mobile_host.'/', $_SERVER['HTTP_REFERER']))
                    {
                        $context->getResponse()->setCookie('nomobile', 1, time() + 60*60*24*30);
                    }
                    else
                    {
                        // redirect to mobile version
                        $context->getController()->redirect('http://'.$mobile_host.$_SERVER['REQUEST_URI']);
                    }
                }
            }

            $context->getResponse()->addStylesheet('/static/css/default.css');
            $context->getResponse()->addStylesheet('/static/css/menu.css');
            $context->getResponse()->addStylesheet('/static/css/print.css', 'print');
        }

        // execute next filter
        $filterChain->execute();
    }
}
