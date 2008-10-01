<?php
/*
 * This file is part of the sfDoctrine package.
 * (c) 2006-2007 Olivier Verdier <Olivier.Verdier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony.plugins
 * @subpackage sfDoctrine
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id: sfDoctrineRecord.class.php 4563 2007-07-09 22:16:05Z Jonathan.Wage $
 */

class sfDoctrineRecord extends Doctrine_Record
{
  public function __toString()
  {
    // if the current object doesn't exist we return nothing
    if (!$this->exists())
    {
      return '-';
    }

    // we try to guess a column which would give a good description of the object
    foreach (array('name', 'title', 'description', 'id') as $descriptionColumn)
    {
      if ($this->getTable()->hasColumn($descriptionColumn))
      {
        return $this->get($descriptionColumn);
      }
    }

    return sprintf('No description for object of class "%s"', $this->getTable()->getComponentName());
  }
  
  // FIXME: All code should be updated to use the new identifier() method directly
  public function obtainIdentifier()
  {
    return $this->identifier();
  }

  // Adapted for c2corg
  public function set($name, $value, $load = true)
  {
    // ucfirst() is used instead of camelize() to be compatible with older version of Doctrine
    $filterMethod = 'filterSet'.ucfirst($name);

    if (method_exists($this, $filterMethod))
    {
      $value = $this->$filterMethod($value);
    }

    $setterMethod = 'set'.sfInflector::camelize($name);

    if (method_exists($this, $setterMethod))
    {
      return $this->$setterMethod($value);
    }
    
    // call custom version of Doctrine_Record::set($name, $value, $load):
        $lower = strtolower($name);

        $lower = $this->_table->getColumnName($lower);

        if (isset($this->_data[$lower])) {
            if ($value instanceof Doctrine_Record) {
                $id = $value->getIncremented();

                if ($id !== null) {
                    $value = $id;
                }
            }

            if ($load) {
                // simpleGet() does not use "filterGet" method
                $old = $this->simpleGet($lower, false);
            } else {
                $old = $this->_data[$lower];
      
                if (method_exists($this, $filterMethod)) {
                    // re-apply "filterSet" method to make sure that old and new values use the same format
                    $old = $this->$filterMethod($old);
                }
            }

            if ($old != $value) { // c2corg change: remove type test (was "!==")
                if ($value === null) {
                    $value = self::$_null;
                }

                $this->_data[$lower] = $value;
                $this->_modified[]   = $lower;
                switch ($this->_state) {
                    case Doctrine_Record::STATE_CLEAN:
                        $this->_state = Doctrine_Record::STATE_DIRTY;
                        break;
                    case Doctrine_Record::STATE_TCLEAN:
                        $this->_state = Doctrine_Record::STATE_TDIRTY;
                        break;
                }
            }
        } else {
            try {
                $this->coreSetRelated($name, $value);
            } catch(Doctrine_Table_Exception $e) {
                throw new Doctrine_Record_Exception("Unknown property / related component '$name'.");
            }
        }
    // end of custom version of Doctrine_Record::set()
  }

  // Added for c2corg
  public function simpleGet($name, $load = true)
  {
    $getterMethod = 'get'.sfInflector::camelize($name);
    return method_exists($this, $getterMethod) ? $this->$getterMethod() : parent::get($name, $load);
  }
  
  // Adapted for c2corg 
  public function get($name, $load = true)
  {
    $value = $this->simpleGet($name, $load);

    // ucfirst() is used instead of camelize() to be compatible with older version of Doctrine
    $filterMethod = 'filterGet'.ucfirst($name);

    if (method_exists($this, $filterMethod))
    {
      $value = $this->$filterMethod($value);
    }

    return $value;
  }

  function rawSet($name, $value, $load = true)
  {
    return parent::set($name, $value, $load);
  }

  function rawGet($name)
  {
    return parent::rawGet($name);
  }

  public function __call($m, $a)
  {
    $verb = substr($m, 0, 3);

    if ($verb == 'set' || $verb == 'get')
    {
      $camelColumn = substr($m, 3);

      if (in_array($camelColumn, array_keys($this->getTable()->getRelations())))
      {
        // the column is actually a class name
        $column = $camelColumn;
      }
      else
      {
        // the relation was not found
        $column = sfInflector::underscore($camelColumn);
      }

      if ($verb == 'get')
      {
        return $this->get($column);
      }
      else // $verb must be 'set'...
      {
        return $this->set($column, $a[0]);
      }
    }

    throw new sfDoctrineException(sprintf('Method "%s" is not defined in class "%s"', $m, get_class($this)));
  }

  // added for compatibility with the _get_options_from_objects helper
  public function getPrimaryKey()
  {
    return $this->obtainIdentifier();
  }

  // to check for the existence of a record
  public function has($name)
  {
    return $this->get($name)->exists();
  }

  // Hook to update created_at and updated_at fields on record insert
  public function preInsert($event)
  {
    // Set created_at and update_at to now, if they exist in this record
    $now = date("Y-m-d H:i:s", time());
    if ($this->getTable()->hasColumn('created_at'))
    {
      $this->rawSet('created_at', $now);
    }
    if ($this->getTable()->hasColumn('updated_at'))
    {
      $this->rawSet('updated_at', $now);
    }
  }

  // Hook to update updated_at field on record update
  public function preUpdate($event)
  {
    // Set update_at to now, if it exists in this record
    $now = date("Y-m-d H:i:s", time());
    if ($this->getTable()->hasColumn('updated_at'))
    {
      $this->rawSet('updated_at', $now);
    }
  }
}
