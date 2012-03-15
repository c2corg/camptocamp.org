<?php
/*
 * Used to specify the range of allowed choices for a select input
 * Available parameters:
 *   @bad_choice_error used to specify the error string
 *   @array_choice an array of authorized values
 *   @config_choice a string that specifies a sfConfig entry. The KEYS are used as authorized values
 *   @array_except an array of values to exclude (useful if config has a 0 entry for example)
 *   @unique defines whether multiple values are allowed or not. Defaults to true (unique value)
 * The different arrays are merged into a single one
 */
class myChoiceValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    // check if we have a list
    $array_choice = $this->getParameter('array_choice', array());

    // check if we have a config entry
    $config_choice = sfConfig::get($this->getParameter('config_choice', null));
    $config_choice = is_null($config_choice) ? array() : array_keys($config_choice);

    // merge the two arrays (no need to array_unique it)
    $choice_list = array_merge($array_choice, $config_choice);

    // check if we have an exception list
    $exception_list = $this->getParameter('array_except', array());

    // whether the entry value is unique or not
    $unique = $this->getParameter('unique', true);

    if (($unique && (is_array($value) ||
                     !in_array($value, $choice_list) ||
                     in_array($value, $exception_list))) ||
        (!$unique && (!is_array($value) ||
                      !count(array_uintersect($value, $choice_list, 'strcmp')) || 
                      count(array_uintersect($value, $exception_list, 'strcmp')))))
    {
      $error = $this->getParameter('bad_choice_error', 'bad choice error');
      return false;
    }

    return true;
  }

  /**
   * Initialize this validator.
   *
   * @param sfContext The current application context.
   * @param array   An associative array of initialization parameters.
   *
   * @return bool true, if initialization completes successfully, otherwise false.
   */
  public function initialize ($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    $this->getParameterHolder()->add($parameters);

    return true;
  }
}
