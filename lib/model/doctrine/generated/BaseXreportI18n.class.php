<?php

class BaseXreportI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('xreports_i18n');

        $this->hasColumn('place', 'string', null);
        $this->hasColumn('route_study', 'string', null);
        $this->hasColumn('conditions', 'string', null);
        $this->hasColumn('training', 'string', null);
        $this->hasColumn('motivations', 'string', null);
        $this->hasColumn('group_management', 'string', null);
        $this->hasColumn('risk', 'string', null);
        $this->hasColumn('time_management', 'string', null);
        $this->hasColumn('safety', 'string', null);
        $this->hasColumn('reduce_impact', 'string', null);
        $this->hasColumn('increase_impact', 'string', null);
        $this->hasColumn('modifications', 'string', null);
        $this->hasColumn('other_comments', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Xreport as Xreport', 'XreportI18n.id');
    }
}
