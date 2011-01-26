<?php
/**
 * $Id: BaseDocumentVersion.class.php 1924 2007-09-29 07:17:18Z fvanderbiest $
 */

class BaseDocumentVersion extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('app_documents_versions');

        $this->hasColumn('documents_versions_id', 'integer', null, array('primary'));
        $this->hasColumn('document_id', 'integer', 11);
        $this->hasColumn('culture', 'string', 2);
        $this->hasColumn('version', 'integer', 11);
        $this->hasColumn('document_archive_id', 'integer', 11);
        $this->hasColumn('document_i18n_archive_id', 'integer', 11);
        $this->hasColumn('nature', 'string', 2); // nature of revision : (to, fo, ft) = (text only, figures only, or figures + text)
        $this->hasColumn('history_metadata_id', 'integer', 11);
        $this->hasColumn('created_at', 'timestamp', null); // update managed by postgres
    }

    public function setUp()
    {
        $this->hasOne('HistoryMetadata as history_metadata', 'DocumentVersion.history_metadata_id');
    
        // FIXME: shortname for archives should be DocumentArchive and DocumentI18nArchive as for other kinds of objects !!!
        $this->hasOne('DocumentArchive as archive', 'DocumentVersion.document_archive_id');
        $this->hasOne('DocumentI18nArchive as i18narchive', 'DocumentVersion.document_i18n_archive_id');

        $modules = array(sfConfig::get('app_modules_list'));
        $modules = array_shift($modules); // to remove "documents" module

        foreach ($modules as $module)
        {
            $model = ucfirst(substr($module, 0, strlen($module)-1));
            $this->hasOne($model.'Archive as '.$model.'Archive', 'DocumentVersion.document_archive_id');
            $this->hasOne($model.'I18nArchive as '.$model.'I18nArchive', 'DocumentVersion.document_i18n_archive_id');
            $this->hasOne($model.' as '.$model, array('local' => 'id', 'foreign' => 'document_id'));
            $this->hasOne($model.'I18n as '.$model.'I18n', array('local' => 'id', 'foreign' => 'document_id'));
        }
        
        // used for filtering 'recent' lists on associated regions (ranges):
        $this->hasMany('GeoAssociation as geoassociations', array('local' => 'document_id', 'foreign' => 'main_id'));        
        $this->hasMany('Association as MainAssociation', array('local' => 'document_id', 'foreign' => 'linked_id'));
        $this->hasMany('Association as LinkedAssociation', array('local' => 'document_id', 'foreign' => 'main_id'));
        
        $this->hasMany('DocumentVersion as versions', array('local' => 'document_id', 'foreign' => 'document_id'));
    }
}
