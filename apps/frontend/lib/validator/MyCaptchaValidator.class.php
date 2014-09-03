<?php
/**
 * $Id$
 */
class MyCaptchaValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $user = sfContext::getInstance()->getUser();
        $g = new Captcha($user->getAttribute('captcha'));
        if ($g->verify($value))
        {
            return true;
        }

        // captcha validation failure => we generate another one
        $g = new Captcha(); 
        $user->setAttribute('captcha', $g->generate());
        $error = $this->getParameter('error');
        return false;
    }
}
