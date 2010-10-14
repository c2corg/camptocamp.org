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

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildConditionItem($conditions, $values, 'Config', '', 'all', 'all', false, $params_list);
        if (isset($conditions['all']) && $conditions['all'])
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildConditionItem($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area', false, $params_list);
        
        // book criteria
        self::buildConditionItem($conditions, $values, 'String', 'mi.search_name', array('bnam', 'name'), null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Istring', 'm.author', 'auth', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Istring', 'm.editor', 'edit', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Array', array('m', 'b', 'book_types'), 'btyp', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Array', array('m', 'b', 'langs'), 'lang', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Array', array('m', 'b', 'activities'), 'act', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'List', 'm.id', 'id', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'List', 'mi.culture', 'bcult', null, false, $params_list);
        
        // linked document criteria
        self::buildConditionItem($conditions, $values, 'List', 'd.linked_id', 'documents', 'join_doc', false, $params_list);

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
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
            self::buildPagerConditions($q, $conditions, $criteria[1]);

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
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions, false);

        if (isset($conditions['join_doc']))
        {
            $q->leftJoin('m.associations d');
            unset($conditions['join_doc']);
        }
        
        $q->addWhere(implode(' AND ', $conditions), $criteria);
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
                      array('author', 'publication_date'));

        return $books;
    }
}
