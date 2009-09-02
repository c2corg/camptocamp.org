<?php
/**
 * $Id: BaseSiteI18n.class.php 1469 2007-08-28 10:07:55Z alex $
 */

class BaseSiteI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('sites_i18n');

        $this->hasColumn('remarks', 'string', null);
        $this->hasColumn('pedestrian_access', 'string', null);
        $this->hasColumn('way_back', 'string', null);
        $this->hasColumn('external_resources', 'string', null);
        $this->hasColumn('site_history', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Site as Site', 'SiteI18n.id');
    }
}
