<?php

class myDateValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('date_error');

    if (!checkdate($value['month'], $value['day'], $value['year']))
      return false;

    $today = date("Ymd");
    $outing_date = date("Ymd", mktime(0, 0, 0, $value['month'], $value['day'], $value['year']));

    return $outing_date <= $today;
  }

}
