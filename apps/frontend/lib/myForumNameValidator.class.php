<?php
/**
 * $Id$
 */

class myForumNameValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $value_temp = trim($value):
        $value_temp = preg_replace('#\s+#', ' ', $value_temp);
        $query = new Doctrine_Query();
        $query->from('UserPrivateData')->where('nickname = ?');
        $res = $query->execute(array(value_temp));

        if (sizeof($res))
        {
            $error = $this->getParameterHolder()->get('nickname_unique_error');
            return false;
        }

        return true;
    }

    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);

        $this->setParameter('nickname_unique_error', 'This nickname already exists. Please choose another one.');
 
        // Set parameters
        $this->getParameterHolder()->add($parameters);
 
        return true;
    }
}
