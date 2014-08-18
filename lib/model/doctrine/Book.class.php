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

    public static function filterSetNb_pages($value)
    {
        return self::returnPosIntOrNull($value);
    }

    public static function buildBookListCriteria(&$criteria, &$params_list, $is_module = false, $prefix = '', $mid = 'm.id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        $m2 = $prefix . 'b';
        if ($is_module)
        {
            $m = 'm';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'book_i18n';
        }
        else
        {
            $m = $m2;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = $prefix . 'book';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
        }
        
        $has_id = false;
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'books'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, $prefix . 'books', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lbc.linked_id', 'btags', 'join_btag_id');
            }
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, $prefix . 'bi.search_name'), ($is_module ? array('bnam', 'name') : $prefix . 'bnam'), array($join_idi18n, $join_i18n), 'Book');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'activities'), $prefix . 'bact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'book_types'), $prefix . 'btyp', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, $m2, 'langs'), $prefix . 'blang', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $prefix . 'bi.culture', $prefix . 'bcult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, $prefix . 'b', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_' . $prefix . 'barticle']))
            {
                $joins['join_' . $prefix . 'book'] = true;
                if (!$is_module)
                {
                    $joins['post_' . $prefix . 'book'] = true;
                }
            }
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
        }
        if (!empty($joins))
        {
            $joins['join_' . $prefix . 'book'] = true;
        }
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        $criteria[2] += $joins;
        
        return null;
    }

    public static function buildListCriteria($params_list)
    {
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'books');
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('bnam'), array('book_i18n', 'join_book'));
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = $conditions;
            $criteria[1] = $values;
            $criteria[2] = $joins;
            $criteria[3] = $joins_order;
            return $criteria;
        }
        
        // area criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', array('g', 'linked_id'), 'areas', 'area_id');
        
        // book / article criteria
        $has_name = Book::buildBookListCriteria($criteria, $params_list, true);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Istring', 'm.author', 'auth', null);
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Istring', 'm.editor', 'edit', null);
        
        // linked doc criteria
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'lbd.linked_id', 'bdocs', 'bdoc');
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false);
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }
    
    public static function buildMainPagerConditions(&$q, $criteria)
    {
    }
    
    public static function buildBookPagerConditions(&$q, &$joins, $is_module = false, $prefix = '', $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = $prefix . 'book';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $linked2 = '';
            $linked_join = $m . '.associations';
        }
        else
        {
            $m = 'l' . $prefix . 'b';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
            }
            $linked_join = $m . '.' . $linked2 . 'LinkedAssociation';
            $join_id = $join . '_id';
            
            if (isset($joins[$join_id]))
            {
                self::joinOnMulti($q, $joins, $join_id, $first_join . " $m", 5);
                
                if (isset($joins[$join_id . '_has']))
                {
                    $q->addWhere($m . "1.type = '$ltype'");
                }
            }
            
            if (   isset($joins['post_' . $join])
                || isset($joins[$join])
                || isset($joins[$join . '_idi18n'])
                || isset($joins[$join . '_i18n'])
            )
            {
                $q->leftJoin($first_join . " $m");
                
                if (   isset($joins['post_' . $join])
                    || isset($joins[$join])
                    || isset($joins[$join . '_i18n'])
                )
                {
                    if ($ltype)
                    {
                        $q->addWhere($m . ".type = '$ltype'");
                    }
                }
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . $linked . 'Book ' . $prefix . 'b');
                }
            }
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'BookI18n ' . $prefix . 'bi');
        }
        
        if (isset($joins['join_' . $prefix . 'barticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, $prefix . 'b', false, $linked_join, 'bc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::joinOnLinkedDocMultiRegions($q, $joins, array(), false);

        // join with book / article tables only if needed 
        if (isset($joins['join_book']))
        {
            Book::buildBookPagerConditions($q, $joins, true);
        }

        if (isset($joins['bdoc']))
        {
            $q->leftJoin('m.associations lbd');
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'bi');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    public static function getSortField($orderby, $mi = 'mi')
    {
        switch ($orderby)
        {
            case 'id':   return 'm.id';
            case 'bnam': return $mi . '.search_name';
            case 'act':  return 'm.activities';
            case 'auth': return 'm.author';
            case 'edit': return 'm.editor';
            case 'btyp': return 'm.book_types';
            case 'blang': return 'm.langs';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('m.author', 'm.activities', 'm.editor', 'm.book_types', 'm.langs', 'm.publication_date');
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
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
