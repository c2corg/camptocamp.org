<?php
/**
 * Checks that uploaded images names are filled in.
 * $Id$
 */
class myImagesNamesValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        foreach ($value as $name)
        {
            $name = trim($name);
            if (empty($name))
            {
                $error = $this->getParameter('empty_field_error');
                return false;
            }
            $namelength = strlen($name);
            if ($namelength < $this->getParameter('min'))
            {
                $error = $this->getParameter('min_error');
                return false;
            }
            if ($namelength > $this->getParameter('max'))
            {
                $error = $this->getParameter('max_error');
                return false;
            }
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
