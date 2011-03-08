<?php
/**
 * $Id$
 */
class myTopoNameValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $value_temp = preg_replace('#\s+#', ' ', $value);
        $value_temp = trim($value_temp);
        $user_id = sfContext::getInstance()->getUser()->getId();
        $query = new Doctrine_Query();
        $query->from('UserPrivateData')->where('id != ? AND topo_name = ?');
        $res = $query->execute(array($user_id, $value_temp));
        if (sizeof($res))
        {
            $error = $this->getParameterHolder()->get('topo_name_unique_error');
            return false;
        }
        return true;
    }
    public function initialize ($context, $parameters = null)
    {
        // Initialize parent
        parent::initialize($context);
        $this->setParameter('topo_name_unique_error', 'This topo_name already exists. Please choose another one.');
 
        // Set parameters
        $this->getParameterHolder()->add($parameters);
 
        return true;
    }
}
