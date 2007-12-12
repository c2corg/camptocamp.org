<?php
/**
 * $Id$
 */

class myLoginNameValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $query = new Doctrine_Query();
        $query->from('UserPrivateData')->where('login_name = ?');
        $res = $query->execute(array(strtolower($value)));

        if (sizeof($res))
        {
            $error = $this->getParameterHolder()->get('login_unique_error');
            return false;
        }

        return true;
    }

    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);

        $this->setParameter('login_unique_error', 'This username already exists. Please choose another one.');
 
        // Set parameters
        $this->getParameterHolder()->add($parameters);
 
        return true;
    }
}
