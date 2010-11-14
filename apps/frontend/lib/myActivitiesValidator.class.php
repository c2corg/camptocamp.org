<?php

class myActivitiesValidator extends sfValidator
{
  public function execute(&$value, &$error)
  {
    $activities_list = sfConfig::get('app_activities_list');

    // whether we allow a document without activity
    if ($this->getParameterHolder()->get('required'))
    {
      unset($activities_list[0]);
    }

    foreach ($value as $activity)
    {
      if (!array_key_exists(intval($activity), $activities_list))
      {
        $error = $this->getParameterHolder()->get('error');
        return false;
      }
    }
    return true;
  }

  public function initialize($context, $parameters = null)
  {
    parent::initialize($context);

    // Initialize parent
    parent::initialize($context);

    // set defaults
    $this->getParameterHolder()->set('required', true);
    $this->getParameterHolder()->set('error', 'You must select at least one activity');

    // Set parameters
    $this->getParameterHolder()->add($parameters);

    return true;
  }
}
