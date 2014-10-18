<?php

// this is the same process that occurs in FormFactorFilter for symfony
// adapted for the forums
if (!$sf_user->hasAttribute('form_factor')) // form factor not determined for this session
{
    // if you make changes here, be sure also to check varnish and FormFactorFilter
    $cookie = $request->getCookie('form_factor');
    if ($cookie === 'mobile' || ($cookie === null && c2cTools::mobileRegexp()))
    {
        $sf_user->setAttribute('form_factor', 'mobile');
    }
    else
    {
        $sf_user->setAttribute('form_factor', 'desktop');
    }
}

$mobile_version = c2cTools::mobileVersion();
