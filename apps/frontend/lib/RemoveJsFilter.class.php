<?php
class RemoveJsFilter extends sfFilter
{
    public function execute($filterChain)
    {
        $context = sfContext::getInstance();
        
        // adapt JS and CSS included for geoportail pages
        if ($context->getRequest()->getParameter('action') == 'geoportail')
        {
            $response = $context->getResponse();

            $response->getParameterHolder()->removeNamespace('helper/asset/auto/stylesheet');
            $response->addStyleSheet('/static/css/main');
            $response->addStyleSheet('/static/css/geoportail');

            $response->getParameterHolder()->removeNamespace('helper/asset/auto/javascript');
        }
    }
}
