<?php
/**
 * $Id: BaseSummitI18n.class.php 1164 2007-08-02 19:51:13Z alex $
 */

class BaseSummitI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('summits_i18n');
    }

    public function setUp()
    {
        $this->hasOne('Summit as Summit', 'SummitI18n.id');
    }
}
