<?php
/**
 * $Id: BaseOutingI18n.class.php 1929 2007-09-29 16:57:26Z alex $
 */

class BaseOutingI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('outings_i18n');

        $this->hasColumn('participants', 'string', 200);
        $this->hasColumn('timing', 'string', 200);
        $this->hasColumn('weather', 'string', null);
        $this->hasColumn('hut_comments', 'string', null);
        $this->hasColumn('access_comments', 'string', null);
        $this->hasColumn('conditions', 'string', null);
        $this->hasColumn('conditions_levels', 'string', null);
        $this->hasColumn('avalanche_desc', 'string', null);
        $this->hasColumn('outing_route_desc', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Outing as Outing', 'OutingI18n.id');
    }
}
