<?php
/**
 * common actions.
 *
 * @package    c2corg
 * @subpackage common
 * @version    SVN: $Id: actions.class.php 2476 2007-12-05 12:46:40Z fvanderbiest $
 */

class commonActions extends c2cActions
{
    /**
     * Executes error404 action
     *
     */
    public function executeError404()
    {
      // show the c2corg 404 error

      // except if host is static host. We really don't want to display standard 404 page to static host:
      // - there would be a lot of links for home & co, but using the static host, thus leading to 403s!
      // - we really don't want symfony to set cookies on static host
      if (sfContext::getInstance()->getRequest()->getHost() == sfConfig::get('app_static_version_host'))
      {
          header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
          header_remove('Set-Cookie');
          echo "<!doctype html>
          <html><head><title>404 Not Found</title></head><body><h1>Not Found</h1>
          <p>File not found on this server.</p></body></html>";
          exit;
      }
    }
    
    /**
     * Executes getInfo action (tooltips on fields)
     *
     */
    public function executeGetinfo()
    {
        $info = $this->__($this->getRequestParameter('elt') . '_info') .
                '<div id="close_info">' . $this->__('close') . '</div>';
        return $this->renderText($info);
    }

    /**
     * Executes edit in place action
     */
    public function executeEdit()
    {
        $text = $this->getRequestParameter('value');
        $culture = $this->getRequestParameter('lang');
        // restricted to moderators in security.yml

        // save text in a db field
        $status = Message::doSave($text, $culture);
        if ($status)
        {
            //$this->clearHomepageCache($culture);
            if (empty($text))
            {
                return $this->renderText($this->__('No message defined. Click to edit'));
            }
            return $this->renderText($text);
        }
        return $this->renderText($this->__('Message setting failed. This message has not been saved.'));
    }

    // switch between mobile and standard version of the site
    public function executeSwitchformfactor()
    {
        $user = $this->getUser();
        if ($user->getAttribute('form_factor', 'desktop') === 'mobile')
        {
            $user->setAttribute('form_factor', 'desktop');
            if (!c2cTools::mobileRegexp())
            {
                // delete form_factor cookie (not needed)
                $this->getResponse()->setCookie('form_factor', null, -1);
            }
            else
            {
                // set cookie so that we are sure to prevent redirection on next sessions
                $this->getResponse()->setCookie('form_factor', 'desktop', time() + 60*60*24*30);
            }
        }
        else
        {
             $user->setAttribute('form_factor', 'mobile');
             if (c2cTools::mobileRegexp())
             {
                 // delete form_factor cookie (not needed)
                 $this->getResponse()->setCookie('form_factor', null, -1);
             }
             else
             {
                 // set cookie so that we are sure to set form factor correctly on next sessions
                 $this->getResponse()->setCookie('form_factor', 'mobile', time() + 60*60*24*30);
             }
        }
        // redirect to referer
        return $this->redirect($this->getRequest()->getReferer());
    }
    
    // set/unset main filter switch by AJAX
    public function executeSwitchallfilters()
    {
        $referer = $this->getRequest()->getReferer();
        
        if (c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            $this->getUser()->setFiltersSwitch(false);
            $message = 'Filters have been deactivated';
        }
        else
        {
            $this->getUser()->setFiltersSwitch(true);
            $message = 'Filters have been activated';
        }
    
        return $this->setNoticeAndRedirect($message, $referer);
    }
    
    // one click site customization    
    public function executeCustomize()
    {
        $referer = $this->getRequest()->getReferer();

        $alist = sfConfig::get('app_activities_list');
        array_shift($alist); // to remove 0

        if ($this->hasRequestParameter('activity'))
        {
            $activity = $this->getRequestParameter('activity', 0) - 1; // comprised between 0 and 7
            /*
            1: skitouring
            2: snow_ice_mixed
            3: mountain_climbing
            4: rock_climbing
            5: ice_climbing
            6: hiking
            7: snowshoeing
            8: paragliding
            */
        }
        else if ($this->hasRequestParameter('activity_name')) // got here by activity_name
        {
            $name = $this->getRequestParameter('activity_name');
            foreach ($alist as $a => $a_name)
            {
                if ($a_name == $name) $activity = $a;
            }
        }
        else
        {
            $activity = -1;
        }

        $user = $this->getUser();
        if ($user->isConnected())
        {
            $user_id = $user->getId();
        }
        else
        {
            $user_id = null;
        }

        if (array_key_exists($activity, $alist))
        {
            if ((c2cPersonalization::getInstance()->getActivitiesFilter() == array($activity+1))
                && ($this->hasRequestParameter('activity')))
            {
                // we disactivate the previously set quick filter on this activity
                c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), array(), $user_id);
                return $this->setNoticeAndRedirect("c2c is no more customized with activies", $referer);
            }
            else
            {
                // we build a simple activity filter with one activity:
                c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), array($activity+1), $user_id);
                // we need to activate main filter switch:
                $user->setFiltersSwitch(true);
                $activity_name = $alist[$activity];
                return $this->setNoticeAndRedirect("c2c customized for $activity_name !", $referer);
            }
        }
        else
        {
            return $this->setNoticeAndRedirect('could not understand your request', $referer);
        }
    }  
    
}
