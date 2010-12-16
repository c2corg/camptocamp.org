<?php
/**
 * $Id: BaseHistoryMetadata.class.php 1148 2007-08-02 13:51:02Z jbaubort $
 */

class BaseHistoryMetadata extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_history_metadata');

        $this->hasColumn('history_metadata_id', 'integer', null, array('primary'));
        $this->hasColumn('user_id', 'integer', 11);
        $this->hasColumn('is_minor', 'boolean');
        $this->hasColumn('comment', 'string', 50);
        $this->hasColumn('written_at', 'timestamp', null); // update managed by postgres
        // et il ne faut pas ecrire dans ce champ depuis le PHP (sinon la gestion des métadonnées d'historique ne marche pas)
    }

    public function setUp()
    {
        $this->hasOne('UserPrivateData as user_private_data', 'HistoryMetadata.user_id');
        $this->hasMany('DocumentVersion as versions', array('local' => 'history_metadata_id', 'foreign' => 'documents_versions_id'));
    }
}
