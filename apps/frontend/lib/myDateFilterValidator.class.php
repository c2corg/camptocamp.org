<?php

class myDateFilterValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $error = $this->getParameter('date_format_error');

    if (empty($value['month'])) return false;

    return true;
  }

}
