<?php

/*
 * This filter is intended to switch between regular and mobile
 * version of the site
 */
class MobileFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = $this->getContext();
        $user = $context->getUser();

        if ($this->isFirstCall() && !$user->hasAttribute('form_factor')) // form factor not determined for this session
        {
            // - Mobile should catch android phones, ipod touch, iphone, ipads (even if we maybe don't want it), windows phone 7 etc
            // - Symbian, Nokia, Blackberry self explainable
            // - SAMSUNG for Bada and other samsung devices
            // - Mini for Opera Mini
            // Maybe we should add more, but it doesn't represent many visits and users can still directly access mobile version of the site
            // if you make changes here, be sure also to check web/forums/include/common.php
            // and check varnish vcl file
            if (preg_match('/(Mobile|Symbian|Nokia|SAMSUNG|BlackBerry|Mini|Android)/i', $_SERVER['HTTP_USER_AGENT']) &&
                !$context->getRequest()->getCookie('nomobile'))
            {
                $user->setAttribute('form_factor', 'mobile');
            }
            else
            {
                $user->setAttribute('form_factor', 'desktop');
            }
        }

        if ($user->getAttribute('form_factor', 'desktop') === 'mobile')
        {
            // change layout for mobile_layout
            $context->getResponse()->setParameter($context->getModuleName().'_'.$context->getActionName().'_layout',
                                                  'mobile_layout', 'symfony/action/view');
            $context->getResponse()->addStylesheet('/static/css/mobile.css', 'last');
        }
        else
        {
            $context->getResponse()->addStylesheet('/static/css/default.css');
            $context->getResponse()->addStylesheet('/static/css/menu.css');
            $context->getResponse()->addStylesheet('/static/css/print.css', 'print');
        }

        // execute next filter
        $filterChain->execute();
    }
}
