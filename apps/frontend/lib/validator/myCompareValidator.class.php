<?php
/**
 * $Id: myCompareValidator.class.php 1272 2007-08-14 18:34:26Z alex $
 */
class myCompareValidator extends sfCompareValidator
{
  public function execute(&$value, &$error)
  {
    $check_param = $this->getParameterHolder()->get('check');
    $check_value = $this->getContext()->getRequest()->getParameter($check_param);
    $comparator  = $this->getParameterHolder()->get('comparator');

    switch($comparator)
    {
        case '==': $result = ($value == $check_value); break;
        case '!=': $result = ($value != $check_value); break;
        case '>=': $result = ($value >= $check_value); break;
        case '<=': $result = ($value <= $check_value); break;
        case '>': $result = ($value > $check_value); break;
        case '<': $result = ($value < $check_value); break;
        default: $result = false;
    }

    if (!$result)
    {   
      $error = $this->getParameterHolder()->get('compare_error');
      return false;
    }   

    return true;
  }
}
