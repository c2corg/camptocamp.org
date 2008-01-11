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
     *
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
            return $this->renderText($text);
        }
        return $this->renderText('Message setting failed. This message has not been saved.');
    }
    
    // set/unset main filter switch by AJAX
    public function executeSwitchallfilters()
    {
        $referer = $this->getRequest()->getReferer();
        
        if (c2cPersonalization::isMainFilterSwitchOn())
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

        switch ($this->getRequestParameter('activity'))
        {
            case 1:
                $activity = array(1); // skitouring
                $aname = 'skitouring';
                break;
            case 2:
                $activity = array(2,3,5); // snow_ice_mixed, mountain_climbing, ice_climbing
                $aname = 'alpi';
                break;
            case 3:
                $activity = array(4); // rock_climbing
                $aname = 'climbing';
                break;
            case 4:
                $activity = array(6); // hiking
                $aname = 'hiking';
                break;                
            default:
                return $this->setNoticeAndRedirect('Could not understand your request', $referer);
        }
        
        // we build a simple activity filter with this activity combination:
        c2cPersonalization::saveFilter(sfConfig::get('app_personalization_cookie_activities_name'), $activity);
        // we need to activate main filter switch:
        $this->getUser()->setFiltersSwitch(true);
        return $this->setNoticeAndRedirect("c2c customized for $aname !", $referer);
    }
    
}
