<?php
/**
 * $Id: components.class.php 1757 2007-09-22 17:55:40Z alex $
 */
 
class documentsComponents extends sfComponents
{
    public function executeGetMsg()
    {
        $user = $this->getUser();
        $timePunbbMsg = $user->getAttribute('TimePunbbMsg', 0);
        $request = $this->getRequest();
    
        if ( (time() - $timePunbbMsg) > (60*5)
                || $timePunbbMsg == 0 
                || (strstr($request->getUri(), 'message_list.php') !== false 
                && $request->hasParameter('id') === true) )
        {
            $this->UnreadMsg = PunbbMsg::GetUnreadMsg($user->getId());
            if (!$this->UnreadMsg) 
            {
                $this->UnreadMsg = 0;
            }
            $user->setAttribute('NbPunbbMsg', $this->UnreadMsg);
            $user->setAttribute('TimePunbbMsg', time());
        }
        else
        {
            $this->UnreadMsg = $user->getAttribute('NbPunbbMsg', 0);
        }
    }
}
