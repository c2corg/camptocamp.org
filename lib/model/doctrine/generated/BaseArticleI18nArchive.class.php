<?php
/**
 * $Id: BaseArticleI18nArchive.class.php 1204 2007-08-08 14:02:31Z alex $
 */

class BaseArticleI18nArchive extends BaseDocumentI18nArchive
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('app_articles_i18n_archives');

        $this->hasColumn('article_i18n_archive_id', 'integer', 11);
        $this->hasColumn('abstract', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('User as userI18n', 'ArticleI18nArchive.user_id_i18n');
    }
}
