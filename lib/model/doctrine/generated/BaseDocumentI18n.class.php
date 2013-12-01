<?php
/**
 * $Id: BaseDocumentI18n.class.php 2247 2007-11-02 13:56:21Z alex $
 */

class BaseDocumentI18n extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('documents_i18n');

        $this->hasColumn('id', 'integer', 10, array('primary'));
        $this->hasColumn('culture', 'string', 2, array('primary'));
        $this->hasColumn('name', 'string', 150);
        $this->hasColumn('search_name', 'string', 150);
        $this->hasColumn('description', 'string', null);
    }

    public function setUp()
    {
        $this->hasOne('Document as Document', 'DocumentI18n.id');
    }

    /**
     * Retrieves a DocumentI18n record with name hydrated for a known (id, culture, model).
     * @param integer document id
     * @param string lang to look for
     * @param string model to look into
     * @return DocumentI18n.
     */
    public static function findName($id, $lang, $model = 'Document')
    {
        return Doctrine_Query::create()
                    ->select('mi.name')
                    ->from($model . 'I18n mi')
                    ->where('mi.id = ? AND mi.culture = ?', 
                            array($id, $lang))
                    ->execute()
                    ->getFirst();
    }
    
    public static function findNameDescription($id, $lang, $model = 'Document')
    {
        return Doctrine_Query::create()
                    ->select('mi.name, mi.description')
                    ->from($model . 'I18n mi')
                    ->where('mi.id = ? AND mi.culture = ?', 
                            array($id, $lang))
                    ->execute()
                    ->getFirst();
    }
    
    public static function findBestDescription($id, $user_prefered_langs, $model = 'Document')
    {
        $results = Doctrine_Query::create()
                    ->select('mi.culture, mi.description')
                    ->from($model . 'I18n mi')
                    ->where('mi.id = ?', array($id))
                    ->execute();
                    
        // build the actual results based on the user's prefered language
        $ref_culture_rank = 10; // fake high value
        foreach ($results as $result)
        {
            $tmparray = array_keys($user_prefered_langs, $result->get('culture')); 
            $rank = array_shift($tmparray);
            if ($rank < $ref_culture_rank && $rank !== null)
            {
                $desc = $result->get('description');
                $ref_culture_rank = $rank;
            }
        }
        
        return $desc;
    }

    public static function findBestName($id, $user_prefered_langs, $model = 'Document')
    {
        $results = Doctrine_Query::create()
                    ->select('mi.culture, mi.name')
                    ->from($model . 'I18n mi')
                    ->where('mi.id = ?', array($id))
                    ->execute();

        // build the actual results based on the user's prefered language
        $ref_culture_rank = 10; // fake high value
        foreach ($results as $result)
        {
            $tmparray = array_keys($user_prefered_langs, $result->get('culture'));
            $rank = array_shift($tmparray);
            if ($rank < $ref_culture_rank && $rank !== null)
            {
                $name = $result->get('name');
                $ref_culture_rank = $rank;
            }
        }

        return $name;
    }

    public static function findBestCulture($id, $user_prefered_langs, $model = 'Document')
    {
        $results = Doctrine_Query::create()
                    ->select('mi.culture')
                    ->from($model . 'I18n mi')
                    ->where('mi.id = ?', array($id))
                    ->execute();
                    
        if (!$results->getFirst())
        {
            return false;
        }
                    
        // build the actual results based on the user's prefered language
        $ref_culture_rank = 10; // fake high value
        foreach ($results as $result)
        {
            $culture = $result->get('culture');
            $rank = array_search($culture, $user_prefered_langs);
            if ($rank === false)
                $rank = 10;

            if ($rank < $ref_culture_rank)
            {
                $out = $culture;
                $ref_culture_rank = $rank;
            }
        }
        return $out;
    }

    /**
     * This filter function is factorized here because the "description" field is used in all I18n modules.
     */
    public static function filterSetDescription($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    // validators look at string length after trim, but be sure to trim it before storing in database
    public static function filterSetName($value)
    {
        return trim($value);
    }
}
