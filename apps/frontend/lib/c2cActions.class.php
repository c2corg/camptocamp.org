<?php
/**
 * c2cActions must be between action in module and the base class sfActions
 * it provides new shortcuts
 *
 * @author     Mickael Kurmann <mickael.kurmann@gmail.com>
 * @version    SVN: $Id: $
 */

abstract class c2cActions extends sfActions
{
    protected function setMessage($name, $message, $vars = NULL, $persist = true)
    {
        c2cTools::log('{' . $name . '} ' . $message);
        $this->setFlash($name, $this->__($message, $vars), $persist);
    }

    protected function setNotice($message, $vars = NULL, $persist = true)
    {
        $this->setMessage('notice', $message, $vars, $persist);
    }

    protected function setWarning($message, $vars = NULL, $persist = true)
    {
        $this->setMessage('warning', $message, $vars, $persist);
    }

    protected function setError($message, $vars = NULL, $persist = true)
    {
        $this->setMessage('error', $message, $vars, $persist);
    }

    protected function setMessageAndRedirect($name, $message, $url, $vars = NULL, $js = NULL, $status_code = 302)
    {
        if ($this->isAjaxCall())
        {
            c2cTools::log('ajax messageAndRedirect | ' . $message);
            $error_remove = "";
            
            if ($name == 'error')
            {
                $this->getResponse()->setStatusCode(404);
            }
            else
            {
                // auto remove error classes on fields
                sfLoader::loadHelpers(array('Javascript', 'Tag'));
                $field_error = sfConfig::get('app_form_field_error');
                $error_remove = javascript_tag("$('.$field_error').removeClass('$field_error');$('.form_error').hide()");
            }

            return $this->renderText($error_remove . $js . $this->__($message, $vars));
        }
        else
        {
            $this->setMessage($name, $message, $vars);
            $this->redirect(empty($url) ? '@homepage' : $url, $status_code);
        }
    }

    protected function handleErrorForAjax()
    {
        $errors = $this->getRequest()->getErrors();
        $field_error = sfConfig::get('app_form_field_error');
        $js_errors = "'#" .implode(", #", array_keys($errors)) . "'";
        $arrow = sfConfig::get('sf_validation_error_prefix', '');

        $this->getResponse()->setStatusCode(404);

        sfLoader::loadHelpers(array('Javascript', 'Tag'));
        // add error class on fields via js
        $js  = "$('.$field_error').removeClass('$field_error');";
        $js .= "$('.form_error').hide();";// remove all errors
        $js .= "$($js_errors).addClass('$field_error');"; // display new ones (if any);

        // global form error
        $toReturn = $this->__('Oups!') . '<ul>';
        foreach($errors as $name => $error)
        {
            $js .= "$('#error_for_$name').html(" . json_encode($arrow . $this->__($error) . $arrow) . ").show();";
            $toReturn .= '<li>' . $this->__($error) . '</li>';
        }
        $toReturn .= '</ul>';

        return $this->renderText(javascript_tag($js).$toReturn);
    }

    // generalist handle error (no need to set handle error on every form error)
    // special handleError can be set when initiating datas is needed
    public function handleError()
    {
        if($this->isAjaxCall())
        {
            return $this->handleErrorForAjax();
        }
        else
        {
            return sfView::SUCCESS;
        }
    }

    protected function setNoticeAndRedirect($message, $url, $vars = NULL, $js = NULL, $status_code = 302)
    {
        return $this->setMessageAndRedirect('notice', $message, $url, $vars, $js, $status_code);
    }

    protected function setWarningAndRedirect($message, $url, $vars = NULL, $js = NULL, $status_code)
    {
        return $this->setMessageAndRedirect('warning', $message, $url, $vars, $js, $status_code);
    }

    protected function setErrorAndRedirect($message, $url, $vars = NULL, $js = NULL, $status_code = 302)
    {
        /* avoid redirection errors, when login form is displayed, but user
           has still insufficient rights when logged in */
        if ($url == $this->getRequest()->getUri())
        {
            $url = null;
        }

        return $this->setMessageAndRedirect('error', $message, $url, $vars, $js, $status_code);
    }

    /**
     * Shortcut for the I18n translation function.
     * @param string
     * @return string
     */
    protected function __($msg, $vars = null)
    {
        return $this->getContext()->getI18N()->__($msg, $vars);
    }

    protected function sendC2cEmail($module_name, $action_name, $email_subject, $email_recipient)
    {
        // Get message body
        $htmlBody = $this->getPresentationFor($module_name, $action_name);
        
        // class initialization
        $mail = new sfMail();
        $mail->setCharset('utf-8');      

        // definition of the required parameters
        $mail->setSender(sfConfig::get('app_outgoing_emails_sender'));
        $mail->setFrom(sfConfig::get('app_outgoing_emails_from'));
        $mail->addReplyTo(sfConfig::get('app_outgoing_emails_reply_to'));

        $mail->addAddress($email_recipient);

        $mail->setSubject($email_subject);
        
        $mail->setContentType('text/html');
        $mail->setBody($htmlBody);
        $mail->setAltBody(strip_tags($htmlBody));

        // send the email
        $mail->send();
    }

    protected function isAjaxCall()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    protected function setPageTitle($custom)
    {
        $this->getResponse()->setTitle($custom . ' - Camptocamp.org');
    }

    protected function removeFromCache($items)
    {
        $cacheManager = $this->getContext()->getViewCacheManager();
        foreach ($items as $item)
        {
            c2cTools::log('{cache} removing : ' . $item);
            $cacheManager->remove($item);
        }
    }

    protected function removeGloballyFromCache($items)
    {
        $cache_dir = sfConfig::get('sf_root_cache_dir') . '/frontend/*/template/*/*';
        $cache_dir .= (sfConfig::get('sf_no_script_name')) ? '/' : '/*/';

        foreach ($items as $item)
        {
            c2cTools::log('{cache} removing : ' . $cache_dir . $item);
            sfToolkit::clearGlob($cache_dir . $item);
        }
    }

    // clear whatsnew indicates whether the list and whatsnew pages should be removed from cache (they have a ong lifetime) 
    protected function clearCache($module_name, $id, $clear_whatsnew = true, $action = '*')
    {
        $module_name = ($module_name=='documents') ? '*' : $module_name;
    
        $toRemove[] = "$module_name/$action/id/$id/*";
        
        if ($module_name == 'portals' && $action == '*')
        {
            $toRemove[] = "documents/_welcome/*";
            $toRemove[] = "documents/_prepare/*";
            $toRemove[] = "portals/_welcome/*";
            $toRemove[] = "portals/_prepare/*";
        }
        
        if ($clear_whatsnew)
        {
            $toRemove[] = "$module_name/whatsnew/*";
            $toRemove[] = "$module_name/list/*";
            $toRemove[] = "documents/whatsnew/*";
        }
        
        $this->removeGloballyFromCache($toRemove);
    }

    protected function clearHomepageCache($lang = '*')
    {
        $this->removeGloballyFromCache(array("documents/home/il/$lang/*"));
    }
    
    /**
     * Check if the field has been setted in sent request and repopulate it
     *
     * @param object objectToUpdate
     * @return object objectToUpdate
     */
    protected function populateFromRequest($object_to_update)
    {
        foreach(Document::getVisibleFieldNamesByModel(get_class($object_to_update)) as $field_name)
        {
            $value = $this->getRequestParameter($field_name);

            if (isset($value))
            {
                $object_to_update->set($field_name, $value);
            }
        }

        return $object_to_update;
    }

    protected function ajax_feedback($msg)
    {
        $this->getResponse()->setStatusCode(404);
        return $this->renderText($this->__($msg));
    }

    protected function ajax_feedback_autocomplete($msg)
    {
        return $this->renderText('<ul><div class="feedback">'.$this->__($msg).'</div></ul>');
    }

    protected function setCacheControl($age = 600)
    {
        $response = $this->getResponse();
        $response->addCacheControlHttpHeader("max_age=$age");
        $response->setHttpHeader('Expires', $response->getDate(time() + $age));
    }

    public static function statsdTiming($stat, $time, $prefix = false, $unit = 'us')
    {
        switch ($unit)
        {
            case 's':
                $time = $time / 1000;
                break;
            case 'ms':
                $time = $time * 1;
                break;
            case 'us':
                $time = $time * 1000;
                break;
            case 'ns':
                $time = $time * 1000000;
                break;
        }

        $_prefix = $prefix ? $prefix : self::statsdPrefix(false, false);
        StatsD::timing($_prefix . $stat, $time);
    }

    public static function statsdIncrement($stat, $prefix = false)
    {
        $_prefix = $prefix ? $prefix : self::statsdPrefix(false, false);
        StatsD::increment($_prefix . $stat);
    }

    public static function statsdPrefix($module = false, $action = false)
    {
        if ($module)
        {
            $moduleName = $module;
        }
        elseif (sfContext::hasInstance() && sfContext::getInstance()->getModuleName() != null)
        {
            $moduleName = sfContext::getInstance()->getModuleName();
        }
        else
        {
            $moduleName = '_nomodule_';
        }

        if ($action)
        {
            $actionName = $action;
        }
        elseif (sfContext::hasInstance() && sfContext::getInstance()->getActionName() != null)
        {
            $actionName = sfContext::getInstance()->getActionName();
        }
        else
        {
            $actionName = '_noaction_';
        }

        $prefix = 'symfony.' .
          sfConfig::get('sf_environment') . '.' .
          $moduleName . '.' .
          $actionName . '.';

        return $prefix;
    }

}
