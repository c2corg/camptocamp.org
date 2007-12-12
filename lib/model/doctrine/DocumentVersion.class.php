<?php
/*
 * $Id: DocumentVersion.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class DocumentVersion extends BaseDocumentVersion
{
    /**
     * To get a specific document version
     *
     * @param int $id
     * @param int $version
     * @param string $culture
     * @return DocumentVersion object
     */
    public static function getByIdAndVersionAndCulture($id, $version, $culture)
    {
        return Doctrine_Query::create()
                             ->from('DocumentVersion d')
                             ->where('d.document_id = ? AND d.culture = ? AND d.version = ?',
                                     array($id, $culture, $version))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }
}
