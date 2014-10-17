<?php

/*
 * This filter is intended to switch between regular and mobile
 * version of the site
 */
class FormFactorFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = $this->getContext();
        $user = $context->getUser();

        if ($this->isFirstCall() && !$user->hasAttribute('form_factor')) // form factor not determined for this session
        {
            // if you make changes here, be sure also to check web/forums/include/common.php
            // and check varnish vcl file
            $cookie = $context->getRequest()->getCookie('form_factor');
            if ($cookie === 'mobile' || ($cookie === null && c2cTools::mobileRegexp()))
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
