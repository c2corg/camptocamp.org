<?php

class myDateFilterValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('date_format_error');

    if (empty($value['month'])) return false;

    if (empty($value['day']) && !empty($value['month']) && !empty($value['year'])) return false;

    return true;
  }

}
