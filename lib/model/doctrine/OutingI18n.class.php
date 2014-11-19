<?php
/**
 * $Id: OutingI18n.class.php 1971 2007-10-03 17:43:34Z alex $
 */
class OutingI18n extends BaseOutingI18n
{
    public static function filterSetConditions_levels($value)
    {   
        if (is_array($value))
        {
            foreach ($value as $level => $data)
            {
                foreach ($data as $field)
                {
                    if (!empty($field))
                    {
                        // if at least one field is not empty, line is not empty:
                        // go directly to next step of first foreach loop (next line)
                        continue 2;
                    }
                }
                // remove empty lines
                unset($value[$level]);
            }
        }

        if (empty($value))
        {
            return NULL;
        }

        $value = array_values($value);

        return serialize($value);
    } 

    public static function filterGetConditions_levels($value)
    {
        return unserialize($value);
    }

    public static function filterSetParticipants($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetTiming($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetWeather($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetHut_comments($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetAccess_comments($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetConditions($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetAvalanche_desc($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }

    public static function filterSetOuting_route_desc($value)
    {
        return BaseDocument::returnNullIfEmpty($value);
    }
}
