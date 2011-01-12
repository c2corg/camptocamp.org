<?php
/**
 * Extension of sfDoctrinePager. Main change is about the "count" query.
 * $Id: $
 */
class c2cDoctrinePager extends sfDoctrinePager {

  protected $countQuery;
  protected $simpleQuery;

  public function __construct($class, $defaultMaxPerPage = 10)
  {
    parent::__construct($class, $defaultMaxPerPage);
    $this->simpleQuery = clone $this->getQuery();
    $this->countQuery = $this->getQuery();
  }

  public function simplifyCounter() {
    $this->countQuery = $this->simpleQuery;
    $this->countQuery->addWhere('redirects_to IS NULL');
  }

  public function simplifyBaseCounter() {
    $this->countQuery = $this->simpleQuery;
  }

  public function init()
  {
    $count = $this->countQuery->offset(0)->limit(0)->count();

    $this->setNbResults($count);

    $p = $this->getQuery();
    $p->offset(0);
    $p->limit(0);
    if ($this->getPage() == 0 || $this->getMaxPerPage() == 0)
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

      $p->offset($offset);
      $p->limit($this->getMaxPerPage());
    }
  }
}
