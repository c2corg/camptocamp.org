<?php
class myPtTypesValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    // check if we have a list
    $array_choice = $this->getParameter('array_choice', array());

    // the void option should not be selected along with other tc types, unless
    // there is only one and it is cablecar
    $config_choice = sfConfig::get('app_parkings_public_transportation_ratings');
    unset($config_choice[0]);
    unset($config_choice[9]);
    $config_choice = array_keys($config_choice);

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
