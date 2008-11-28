<?php

class myDateTimeValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('datetime_error');

    if (!is_numeric($value['hour']) || !is_numeric($value['minute']) || !is_numeric($value['second']) ||
        !is_numeric($value['month']) || !is_numeric($value['day']) || !is_numeric($value['year']))
      return false;

    if (checkdate($value['month'], $value['day'], $value['year']) == false)
      return false;

    $now = time();
    $date = mktime($value['hour'], $value['minute'], $value['second'], $value['month'], $value['day'], $value['year']);

    if ($date == false || $date > $now)
      return false;

    return true;
  }
}
