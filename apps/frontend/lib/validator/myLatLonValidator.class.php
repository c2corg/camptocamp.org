<?php
/**
 * chec that both lon and lat are set or empty, but not one set and the other on empty
 */

class myLatLonValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        $lat_or_lon = $this->getParameterHolder()->get('check');
        $check_value = $this->getContext()->getRequest()->getParameter($lat_or_lon);

        // the lat or lon we check is not empty, the other must not be empty
        if (empty($check_value))
        {
            $error = $this->getParameterHolder()->get('lat_or_lon_alone_error');
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
