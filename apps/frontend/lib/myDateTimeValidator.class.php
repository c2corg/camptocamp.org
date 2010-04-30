<?php

class myDateTimeValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('datetime_error');

    $year    = $value['year'];
    $month   = $value['month'];
    $day     = $value['day'];
    $hour    = empty($value['hour']) ? 0 : $value['hour'];
    $minute  = empty($value['minute']) ? 0 : $value['minute'];
    $second  = empty($value['second']) ? 0 : $value['second'];

    // all date elements must be filled
    if (!is_numeric($month) || !is_numeric($day) || !is_numeric($year))
      return false;

    if (checkdate($month, $day, $year) == false)
      return false;

    $now = time();
    $date = mktime($hour, $minute, $second, $month, $day, $year);

    if ($date == false || $date > $now)
      return false;

    return true;
  }
}
