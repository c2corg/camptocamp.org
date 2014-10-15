<?php

// used to customize the cache namespace and add the mobile|standard version of the site as parameter
// unfortunately, symfony 1.0 has several limitations (we cannot replace sfViewCacheManager in factories.yml
// nor have access to sfViewCacheManager::generateCacheKey when using sf_cache_namespace_callable in settings.yml
class CacheNamespace
{
    // customize the cache namespace
    // unfortunately, with symfony 1.0, we cannot reuse the generateCacheKey from sfViewCacheManager
    // so quite a bit of code is duplicated (see https://gist.github.com/damienalexandre/829183)
    // WARNING in order to keep this short, we make some assumptions (as it is not used atm):
    // - we don't check for contextual partials
    // - we don't look for vary headers
    public static function generate($internalUri)
    {
        $context = sfContext::getInstance();
        $user = $context->getUser();
        $controller = $context->getController();
        $request = $context->getRequest();

        // we want our URL with / only
        $oldUrlFormat = sfConfig::get('sf_url_format');
        sfConfig::set('sf_url_format', 'PATH');
        $uri = $controller->genUrl($internalUri);
        sfConfig::set('sf_url_format', $oldUrlFormat);

        // prefix with hostname
        $hostName = $request->getHost();
        $hostName = preg_replace('/[^a-z0-9]/i', '_', $hostName);
        $hostName = strtolower(preg_replace('/_+/', '_', $hostName));

        // add mobile or standard version
        $form_factor = $user->getAttribute('form_factor', 'desktop'); 

        $uri = '/'.$hostName.'/'.$form_factor.'/'.$uri;

        // replace multiple /
        $uri = preg_replace('#/+#', '/', $uri);

        return array(dirname($uri), basename($uri));
    }
}
