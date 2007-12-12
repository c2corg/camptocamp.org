<?php
/**
 * $Id: BaseArticleI18n.class.php 1204 2007-08-08 14:02:31Z alex $
 */

class BaseArticleI18n extends BaseDocumentI18n
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->setTableName('articles_i18n');

        $this->hasColumn('abstract', 'string', null);
    }

    public function setUp()
    {
        $this->ownsOne('Article as Article', 'ArticleI18n.id');
    }
}
