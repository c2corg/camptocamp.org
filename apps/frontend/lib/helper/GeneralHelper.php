<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($search_name)
{
    $slug = html_entity_decode($search_name, ENT_QUOTES, 'UTF-8');
    $pattern = array('~[\W\s_]+~u', '~[^-\w]+~');
    $replace = array('-', '');
    $slug = preg_replace($pattern, $replace, $slug);
    $slug = trim($slug, '-');
    $slug = substr($slug, 0, 100);

    if ($slug == '') $slug = '-';

    return $slug;
}

function get_slug($document)
{
    return formate_slug($document->get('search_name'));
}

function _keep_in_slug($str)
{
    return !is_null($str) && $str != '';
}

/**
 * This function is meant to copy the behaviour of update_search_name()
 * defined in data/sql/db_functions
 * It is used when the 'search_name' field is not available (whereas 'name' is)
 */
function search_name($name)
{
    $search = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ç','Č','ç','č','È','É','Ê','Ë','è','é','ê','ë','Ì','Í',
                    'Î','Ï','ì','í','î','ï','Ñ','ñ','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','Š','š','Ù','Ú','Û','Ü',
                    'ù','ú','û','ü','Ý','Ϋ','ý','ÿ','Ž','ž');
    $replace = array('A','A','A','A','A','A','a','a','a','a','a','a','C','C','c','c','E','E','E','E','e','e','e','e','I','I',
                     'I','I','i','i','i','i','N','n','O','O','O','O','O','O','o','o','o','o','o','o','S','s','U','U','U','U',
                     'u','u','u','u','Y','Y','y','y','Z','z');
    return strtolower(str_replace($search, $replace, $name));
}
