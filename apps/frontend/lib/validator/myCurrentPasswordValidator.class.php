<?php
/**
 * $Id$
 */
class myCurrentPasswordValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $user_id = sfContext::getInstance()->getUser()->getId();
        $user_private_data = UserPrivateData::find($user_id);
        if (!myUser::check_password($value, $user_private_data->password))
        {
            $error = $this->getParameterHolder()->get('bad_password_error');
            return false;
        }
        return true;
    }
    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);
 
        // Set parameters
        $this->getParameterHolder()->add($parameters);
 
        return true;
    }
}
