<?php
/**
 * $Id: BaseParkingI18n.class.php 1203 2007-08-08 10:52:10Z alex $
 */

class BaseParkingI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('Parkings_i18n');

        $this->hasColumn('public_transportation_description', 'string', null);
        $this->hasColumn('snow_clearance_comment', 'string', null);
        $this->hasColumn('accommodation', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Parking as Parking', 'ParkingI18n.id');
    }
}
