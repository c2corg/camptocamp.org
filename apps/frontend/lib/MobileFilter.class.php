<?php

/*
 * This filter is intended to switch between regular and mobile
 * version of the site, based on the hostname
 */
class MobileFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $app_static_url = sfConfig::get('app_static_url');
        $context = $this->getContext();
        if (c2cTools::mobileVersion())
        {
            // change layout for mobile_layout
            $context->getResponse()->setParameter($context->getModuleName().'_'.$context->getActionName().'_layout', 'mobile_layout', 'symfony/action/view');
            $context->getResponse()->addStylesheet($app_static_url . '/static/css/mobile.css', 'last', array('media' => 'all'));
        }
        else
        {
            $context->getResponse()->addStylesheet($app_static_url . '/static/css/menu.css', '', array('media' => 'all'));
            $context->getResponse()->addStylesheet($app_static_url . '/static/css/print.css', 'last', array('media' => 'print'));
        }

        // execute next filter
        $filterChain->execute();
    }
}
