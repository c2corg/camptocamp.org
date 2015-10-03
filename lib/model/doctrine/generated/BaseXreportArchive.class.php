<?php

class BaseXreportArchive extends BaseDocumentArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_xreports_archives');

        $this->hasColumn('date', 'date', null);
        $this->hasColumn('activities', 'string', null); // array
        $this->hasColumn('nb_participants', 'smallint', 3); 
        $this->hasColumn('nb_impacted', 'smallint', 3); 
        $this->hasColumn('severity', 'smallint', 1);
        $this->hasColumn('rescue', 'boolean', null);
        $this->hasColumn('event_type', 'string', null); // array
        $this->hasColumn('author_status', 'smallint', 1);
        $this->hasColumn('activity_rate', 'smallint', 1);
        $this->hasColumn('nb_outings', 'smallint', 1);
        $this->hasColumn('autonomy', 'smallint', 1);
        $this->hasColumn('age', 'smallint', 1);
        $this->hasColumn('gender', 'smallint', 1);
        $this->hasColumn('previous_injuries', 'smallint', 1);
    }

    public function setUp()
    {
        $this->hasOne('DocumentVersion as document_version', 'DocumentVersion.document_archive_id');
    }
}
