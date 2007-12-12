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
    
}
