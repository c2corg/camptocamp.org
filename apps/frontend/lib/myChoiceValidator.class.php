<?php
/*
 * Used to specify the range of allowed choices for a select input
 * Available parameters:
 *   @bad_choice_error used to specify the error string
 *   @array_choice an array of authorized values
 *   @config_choice a string that specifies a sfConfig entry. The KEYS are used as authorized values
 *   @array_except an array of values to exclude (useful if config has a 0 entry for example)
 *   @unique defines whether multiple values are allowed or not. Defaults to true (unique value)
 *   @array_exclusive an array of exclusive values for a multiple choice
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

    // check if this is lega values
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

    // check if we have an exclusive list
    $exclusive_list = $this->getParameter('array_exclusive', array());
    $exclusive_error = false;
    if (!$unique && count($exclusive_list))
    {
        $diff_list = array_udiff($choice_list, $exclusive_list, 'strcmp');
        if (count(array_uintersect($value, $exclusive_list, 'strcmp')) && count(array_uintersect($value, $diff_list, 'strcmp')))
        {
            $error = $this->getParameter('exclusive_choice_error', 'bad choice error');
            return false;
        }
    }

    // check if we have an inclusive list
    $inclusive_list = $this->getParameter('array_inclusive', array());
    $inclusive_error = false;
    if (count($inclusive_list))
    {
        $diff_list = array_udiff($choice_list, $inclusive_list, 'strcmp');
        if (count(array_uintersect($value, $inclusive_list, 'strcmp')) && !count(array_uintersect($value, $diff_list, 'strcmp')))
        {
            $error = $this->getParameter('inclusive_choice_error', 'bad choice error');
            return false;
        }
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
