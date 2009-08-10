<?php
/*
 * Based on sfDoctrineUniqueValidator
 * See plugins/sfDoctrine/lib
 */
class myUniqueValidator extends sfValidator
{
  public function execute (&$value, &$error)
  {
    $className  = $this->getParameter('class');
    $columnName = $className.'.'.$this->getParameter('column');

    $primaryKeys =  sfDoctrine::getTable($className)->getPrimaryKeys();
    foreach($primaryKeys as $primaryKey)
    {
        if(is_null($primaryKeyValue = $this->getContext()->getRequest()->getParameter($primaryKey)));
        break;
    }

    $query = new Doctrine_Query();
    $query->from($className);

    $value = strtolower($value);
    if($primaryKeyValue === null)
    {
        $query->where($columnName.' = ?');
        $res = $query->execute(array($value));
    }
    else
    {
        $query->where($columnName.' = ? AND '.$primaryKey.' != ?');
        $res = $query->execute(array($value, $primaryKeyValue));
    }

    if(sizeof($res))
    {
      $error = $this->getParameterHolder()->get('unique_error');
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

    // set defaults
    $this->setParameter('unique_error', 'Uniqueness error');

    $this->getParameterHolder()->add($parameters);

    // check parameters
    if (!$this->getParameter('class'))
    {
      throw new sfValidatorException('The "class" parameter is mandatory for the myUniqueValidator validator.');
    }

    if (!$this->getParameter('column'))
    {
      throw new sfValidatorException('The "column" parameter is mandatory for the myUniqueValidator validator.');
    }

    return true;
  }
}
