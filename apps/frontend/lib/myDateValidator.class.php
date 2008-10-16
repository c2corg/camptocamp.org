<?php

class myDateValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('date_error');

    if (!checkdate($value['month'], $value['day'], $value['year']))
      return false;

    $today = date("Ymd");
    $outind_date = $value['year'].$value['month'].$value['day'];

    return $outing_date < $today;
  }

}
