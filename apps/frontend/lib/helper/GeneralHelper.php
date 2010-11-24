<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($name)
{
    $slug = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
    $pattern = array('~[\W\s_]+~u', '~[^-\w]+~');
    $replace = array('-', '');
    $slug = preg_replace($pattern, $replace, $slug);
    $slug = trim($slug, '-');
    $slug = substr($slug, 0, 100);

    if ($slug == '') $slug = '-';
    if (is_numeric($slug)) $slug .= '-';

    return $slug;
}

function get_slug($document)
{
    return make_slug($document->get('name'));
}

function make_slug($name)
{
	$name = html_entity_decode($name, ENT_QUOTES, 'UTF-8'); // TODO: prevent escaping of incoming string?
	return formate_slug(remove_accents($name));
}

function _keep_in_slug($str)
{
    return !is_null($str) && $str != '';
}

/**
 * This function is meant to copy the behaviour of remove_accents()
 * defined in data/sql/db_functions
 */
function remove_accents($name)
{
    $search = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ç','Č','ç','č','È','É','Ê','Ë','è','é','ê','ë','Ì','Í',
                    'Î','Ï','ì','í','î','ï','Ñ','ñ','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','Š','š','Ù','Ú','Û','Ü',
                    'ù','ú','û','ü','Ý','Ϋ','ý','ÿ','Ž','ž', 'œ', 'Œ', 'æ', 'Æ');
    $replace = array('A','A','A','A','A','A','a','a','a','a','a','a','C','C','c','c','E','E','E','E','e','e','e','e','I','I',
                     'I','I','i','i','i','i','N','n','O','O','O','O','O','O','o','o','o','o','o','o','S','s','U','U','U','U',
                     'u','u','u','u','Y','Y','y','y','Z','z', 'oe', 'oe', 'ae', 'ae');
    return strtolower(str_replace($search, $replace, $name));
}

/*
* This fonction formate a <span> element to show a picto.
*/
function picto_tag($picto_name, $title = '', $options = null)
{
    $picto_class = 'picto ' . $picto_name;

    if (!is_null($options))
    {
        if (array_key_exists('class', $options))
        {
            $options['class'] .= ' ' . $picto_class;
        }
        else
        {
            $options['class'] = $picto_class;
        }
        
        if (!array_key_exists('title', $options) && !empty($title))
        {
            $options['title'] = $title;
        }
    }
    else
    {
        $options = array('class' => $picto_class);
        if (!empty($title))
            $options['title'] = $title;
    }
    
    return content_tag('span', '', $options);
}

function _implode($glue, $items)
{
    foreach($items as $key => $item)
    {
        if (empty($item) || !is_string($item))
        {
            unset($items[$key]);
        }
    }
    return implode($glue, $items);
}
