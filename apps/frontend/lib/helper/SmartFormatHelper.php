<?php
/**
 * This helper formats input strings so as to generate internal links
 * $Id: SmartFormatHelper.php 1132 2007-08-01 14:38:06Z fvanderbiest $
 */

if (isset($sf_user)) 
{
    // we are in a template 
    // I18N already loaded with every template (settings.yml)
    use_helper('Tag', 'Url'); 
}
else 
{
    // we are in an action
    sfLoader::loadHelpers(array('Tag', 'Url', 'I18N'));
}


/*
 * This function says whether $s is a valid module or not
 */
function is_valid_module($s)
{
    return in_array($s, sfConfig::get('app_modules_list'));
}

/*
 * This function says whether $s is a valid culture or not
 */
function is_valid_culture($s)
{
    return array_key_exists($s, sfConfig::get('app_languages_c2c'));
}

/*
 * This function extracts routing parameters from a string such as 12/fr/2 and outputs a string for link_to
 * If input is not valid string, returns a string for search
 *
 *   if input matches:      output becomes:
 *       summits/12/fr/2 -> @document_by_id_lang_version?module=summits&id=12&lang=fr&version=2
 *       12/fr/2         -> @document_by_id_lang_version?module=documents&id=12&lang=fr&version=2
 *       summits/12/fr   -> @document_by_id_lang?module=summits&id=12&lang=fr
 *       summits/12/fr/slug -> @document_by_id_lang_slug?module=summits&id=12&lang=fr&slug=slug
 *       12/fr           -> @document_by_id_lang?module=documents&id=12&lang=fr
 *       summits/12      -> @document_by_id?module=summits&id=12
 *       12              -> @document_by_id?module=documents&id=12
 *   else                -> @search?q=$s  
 */
function extract_route($s)
{
    $out = '@search?q='.$s;
    
    // traitement des urls erronees - a supprimer lorsque ce sera fait a l'enregistrement
    $base_url  = str_replace('://', ':', $_SERVER['SERVER_NAME']);
    $s = str_replace('://', ':', $s);
    $a = explode('/', $s); // ligne a conserver
    if ((count($a) > 1) && (empty($a[0]) || (strpos($base_url, $a[0]) !== false)))
    {
        array_shift($a);
    } // fin du code a supprimer
    $c = count($a);
    
    if (is_numeric($a[0])) // 12/fr/2 ou 12/fr ou 12
    {
        if ($c == 1) // 12
        { 
            return '@document_by_id?module=documents&id=' . $a[0];
        }
        elseif (is_valid_culture($a[1])) // 12/fr or 12/fr/2
        {
            if ($c == 2) // 12/fr
            {
                return '@document_by_id_lang?module=documents&id=' . $a[0] . '&lang=' . $a[1];
            }
            elseif ($c == 3 && is_numeric($a[2]))  // 12/fr/3
            {
                return '@document_by_id_lang_version?module=documents&id=' . $a[0] .
                       '&lang=' . $a[1] . '&version=' . $a[2];
            }
        }
    }
    else // summits/12/fr/2 or summits/12/fr or summits/12 or mont blanc or toto/bidon
    {
        if ($c == 1) // mont blanc
        { 
            return $out;
        }
        elseif (is_valid_module($a[0]) && is_numeric($a[1])) // summits/12/fr/2 or summits/12/fr or summits/12
        {
            if ($c == 2) // summits/12
            {
                    return '@document_by_id?module=' . $a[0] . '&id=' . $a[1]; 
            }
            elseif (is_valid_culture($a[2]))
            {
                if ($c == 3) // summits/12/fr
                {
                    return '@document_by_id_lang?module=' . $a[0] . '&id=' . $a[1] . '&lang=' . $a[2];
                }
                
                if (is_numeric($a[3])) // summits/12/fr/3
                {
                    return '@document_by_id_lang_version?module=' .$a[0] . '&id=' . $a[1] .
                           '&lang=' . $a[2] . '&version=' . $a[3];
                }

                // summits/12/fr/slug
                return '@document_by_id_lang_slug?module=' . $a[0] . '&id=' . $a[1] . '&lang=' . $a[2] . '&slug=' . $a[3];
            }
        }
    }

    return $out;
}

/*
 * This function extracts the language out of a string returned by extract_route
 * If no language is found, returns null
 */
function extract_lang($route)
{
    preg_match('/lang=(\w{2})/', $route, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}

/*
 * This function formats an input string with links, 
 * and eventually translates the other parts of the input string
 * if mode = 'translation'
 */
function parse_links($s, $mode = 'no_translation', $nl_to_br = false)
{
    $s = ($nl_to_br) ? nl2br($s) : $s;
    
    //split the entire text string on occurences of [[
    $a = explode('[[', $s);
    $out[] = ($mode == 'translation') ? __(array_shift($a)) : array_shift($a);
    
    foreach ($a as $e)
    {
        $p = strpos($e,'|');
        $d = strpos($e,']]');
        
        // what happens if there is no ending ]] (ie $d = 0) ?
        if ($d == 0)
        {
            // display only the opening [[
            $end = '[['.$e;
        }
        else
        {
            if ($p && $p < $d)  
            {
                // "[[12|mont blanc]]" or "[[summits/10|toto]]" 
                $string = substr($e, $p + 1, $d - $p -1);
                $link_part = explode('#', substr($e, 0, $p), 2);
                $anchor = (count($link_part) > 1) ? '#'.$link_part[1] : '';
                $link = extract_route($link_part[0]) . $anchor;
            }
            else
            {
                // "[[12]]" or "[[mont blanc]] toto| truc" or "[[|toto]]"
                $string = ($p === 0) ? substr($e, 1, $d-1) : substr($e, 0, $d) ;
                $link = extract_route($string); 
            }
            $lang = extract_lang($link);
            // we don't set hreflang if the target has the same culture
            $lang = ($lang == sfContext::getInstance()->getUser()->getCulture()) ? '' : $lang;
            $out[] = empty($lang) ? link_to($string, $link): link_to($string, $link, array('hreflang' => $lang));
            $end = substr($e, $d + 2);
        }
        $out[] = ($mode == 'translation') ? __($end) : $end;
    }
    return implode($out);
}


/*
 * This function formats an input string with links, and translates the other parts of the input string
 * useful for automatic comments formatting 
 * Kept for backward compatibility and as a shortcut to parse.
 */
function smart_format($string)
{
    return parse_links($string, 'translation', false);
}
