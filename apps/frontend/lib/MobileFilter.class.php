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
            $context->getResponse()->addStylesheet('/static/css/mobile.css', 'last', array('media' => 'all'));
        }
        else
        {
            $context->getResponse()->addStylesheet('/static/css/menu.css', '', array('media' => 'all'));
            $context->getResponse()->addStylesheet('/static/css/print.css', 'last', array('media' => 'print'));
        }

        // execute next filter
        $filterChain->execute();
    }
}
