<?php
/**
 * $Id: Book.class.php 2535 2007-12-19 18:26:27Z alex $
 */
class Book extends BaseBook
{
    public static function filterSetActivities($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetActivities($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetLangs($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetLangs($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetBook_types($value)
    {   
        return self::convertArrayToString($value);
    }   

    public static function filterGetBook_types($value)
    {   
        return self::convertStringToArray($value);
    }

    public static function filterSetAuthor($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetEditor($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetUrl($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetIsbn($value)
    {
        return self::returnNullIfEmpty($value);
    }
    
    protected static function joinOnMultiRegions($q, $conditions)
    {
        if (isset($conditions['join_area']))
        {
            $q->leftJoin('m.associations l')
              ->leftJoin('l.LinkedDocument d')
              ->addWhere("l.type IN ('bs', 'br', 'bh', 'bt')");
            
            $conditions = Document::joinOnMulti($q, $conditions, 'join_area', 'd.geoassociations g', 3);
        }
        return $conditions;
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('Book', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        $conditions = array();
        $all = false;
        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            if (isset($conditions['all']))
            {
                $all = $conditions['all'];
                unset($conditions['all']);
            }
        }
        
        if (!$all && !empty($conditions))
        {
            $conditions = Book::joinOnMultiRegions($q, $conditions);

            $q->addWhere(implode(' AND ', $conditions), $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->isMainFilterSwitchOn())
        {
            self::filterOnActivities($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   

    protected static function buildFieldsList()
    {   
        $book_field_list = array('m.author', 'm.activities', 'm.editor', 'm.book_types', 'm.langs');
        
        return array_merge(parent::buildFieldsList(),
                           $book_field_list);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::filterOnActivities($q);
    }

    public static function getAssociatedBooksData($associated_docs)
    {
         $books = Document::fetchAdditionalFieldsFor(
                      array_filter($associated_docs, array('c2cTools', 'is_book')),
                      'Book',
                      array('author'));

        return $books;
    }
}
