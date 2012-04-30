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

    public static function buildBookListCriteria(&$conditions, &$values, $params_list, $is_module = false, $prefix = '')
    {
        $m2 = $prefix . 'b';
        if ($is_module)
        {
            $m = 'm';
            $join = null;
            $join_i18n = 'join_book_i18n';
        }
        else
        {
            $m = $m2;
            $join = 'join_' . $prefix . 'book';
            $join_i18n = $join . '_i18n';
        }
        
        $has_id = false;
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, 'List', 'm.id', 'id', null, false, $params_list);
        }
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'List', 'lbc.linked_id', 'btags', 'join_btag_id', false, $params_list);
            }
            self::buildConditionItem($conditions, $values, 'String', $prefix . 'bi.search_name', ($is_module ? array('bnam', 'name') : $prefix . 'bnam'), $join_i18n, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'activities'), $prefix . 'bact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'book_types'), $prefix . 'btyp', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, $m2, 'langs'), $prefix . 'blang', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $prefix . 'bi.culture', $prefix . 'bcult', $join_i18n, false, $params_list);
        }
    }

    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'bcult');
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildConditionItem($conditions, $values, 'Multilist', array('g', 'linked_id'), 'areas', 'join_area', false, $params_list);
        
        // book criteria
        Book::buildBookListCriteria($conditions, $values, $params_list, true);
        self::buildConditionItem($conditions, $values, 'Istring', 'm.author', 'auth', null, false, $params_list);
        self::buildConditionItem($conditions, $values, 'Istring', 'm.editor', 'edit', null, false, $params_list);
        
        // linked doc criteria
        self::buildConditionItem($conditions, $values, 'List', 'lbd.linked_id', 'bdocs', 'join_bdocs_id', false, $params_list);
        
        // image criteria
        Image::buildImageListCriteria($conditions, $values, $params_list, false);

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
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('books'))
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
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions, array(), false);

        if (isset($conditions['join_book_i18n']))
        {
            $q->leftJoin('m.BookI18n bi');
            unset($conditions['join_book_i18n']);
        }

        if (isset($conditions['join_bdocs_id']))
        {
            $q->leftJoin('m.associations lbd');
            unset($conditions['join_bdocs_id']);
        }

        if (isset($conditions['join_btag_id']))
        {
            $q->leftJoin('m.associations lbc');
            unset($conditions['join_btag_id']);
        }

        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'bi');
        }
        
        $q->addWhere(implode(' AND ', $conditions), $criteria);
    }

    protected static function buildFieldsList()
    {   
        $book_field_list = array('m.author', 'm.activities', 'm.editor', 'm.book_types', 'm.langs', 'm.publication_date');
        
        return array_merge(parent::buildFieldsList(),
                           $book_field_list);
    }

    protected function addPrevNextIdFilters($q, $model)
    {
        self::filterOnActivities($q);
    }

    public static function getAssociatedBooksData($associated_docs)
    {
        //$fields = array('activities', 'book_types', 'author', 'publication_date');
        $fields = array('author', 'publication_date');
        $books = Document::fetchAdditionalFieldsFor(
                      array_filter($associated_docs, array('c2cTools', 'is_book')),
                      'Book',
                      $fields);

        return $books;
    }
}
