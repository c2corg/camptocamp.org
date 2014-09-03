<?php
class myPtTypesValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
        // list of possible pt_types, excluding 0 (=no choice)
        // and 9 (cablecar, specific behaviour)
        $config_choice = sfConfig::get('app_parkings_public_transportation_ratings');
        unset($config_choice[0]);
        unset($config_choice[9]);
        $config_choice = array_keys($config_choice);

        // check that pt ratings is compliant with a pt types
        $pt_rating_param = $this->getParameterHolder()->get('pt_rating');
        $pt_rating_value = $this->getContext()->getRequest()->getParameter($pt_rating_param);
        if (($pt_rating_value == "0" || $pt_rating_value == "3") && sizeof(array_intersect($config_choice, $value)))
        {
            $error = $this->getParameter('pt_rating_choice_error', 'bad choice error');
            return false;
        }
    
        // the void option should not be selected along with other tc types, unless
        // there is only one and it is cablecar
        if (in_array(0, $value) && sizeof(array_intersect($config_choice, $value)))
        {
            $error = $this->getParameter('exclusive_choice_error', 'bad choice error');
            return false;
        }

        return true;
    }

    public function initialize ($context, $parameters = null)
    {
        // initialize parent
        parent::initialize($context);

        $this->getParameterHolder()->add($parameters);

        return true;
    }
}
