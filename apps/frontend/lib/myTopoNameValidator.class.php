<?php
/**
 * $Id$
 */

class myTopoNameValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $value_temp = trim($value);
        $value_temp = preg_replace('#\s+#', ' ', $value_temp);
        $query = new Doctrine_Query();
        $query->from('UserPrivateData')->where('topo_name = ?');
        $res = $query->execute(array(value_temp));

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
