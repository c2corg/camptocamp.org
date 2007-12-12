<?php
/**
 * $Id: BaseParkingI18nArchive.class.php 1203 2007-08-08 10:52:10Z alex $
 */

class BaseParkingI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_parkings_i18n_archives');

        $this->hasColumn('parking_i18n_archive_id', 'integer', 11);
        $this->hasColumn('public_transportation_description', 'string', null);
        $this->hasColumn('snow_clearance_comment', 'string', null);
        $this->hasColumn('accommodation', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'ParkingI18nArchive.user_id_i18n');
    }
}
