<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($name)
{
    $slug = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
    // remove non word characters or -, and replace spaces and non word characters by -
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
 * This function is used for creating slugs and
 * sorting alphabetically documents
 */
function remove_accents($name)
{
    $search = array('À','Á','Â','Ã','Ä','Å','Ǻ','Ā','Ă','Ą','Ǎ',
                    'à','á','â','ã','ä','å',
                    'Ç','Ć','Ĉ','Ċ','Č',
                    'ç','ć','ĉ','ċ','č',
                    'Ð','Ď','Đ','ð','ď','đ',
                    'È','É','Ê','Ë','Ē','Ĕ','Ė','Ę','Ě',
                    'è','é','ê','ë','ē','ĕ','ė','ę','ě',
                    'ƒ','Ĝ','Ğ','Ġ','Ģ',
                    'ĝ','ğ','ġ','ģ',
                    'Ĥ','Ħ','ĥ','ħ',
                    'Ì','Í','Î','Ï','Ĩ','Ī','Ĭ','Ǐ','Į','İ',
                    'ì','í','î','ï','ĩ','ī','ĭ','ǐ','į','ı',
                    'Ĵ','ĵ',
                    'Ķ','ķ',
                    'Ĺ','Ļ','Ľ','Ŀ','Ł',
                    'ĺ','ļ','ľ','ŀ','ł',
                    'Ñ','Ń','Ņ','Ň',
                    'ñ','ń','ņ','ň','ŉ',
                    'Ö','Ò','Ó','Ô','Õ','Ō','Ŏ','Ǒ','Ő','Ơ','Ø','Ǿ',
                    'ö','ò','ó','ô','õ','ō','ŏ','ǒ','ő','ơ','ø','ǿ','º',
                    'Ŕ','Ŗ','Ř','ŕ','ŗ','ř',
                    'Ś','Ŝ','Ş','Š','ß',
                    'ś','ŝ','ş','š','ſ',
                    'Ţ','Ť','Ŧ','ţ','ť','ŧ',
                    'Ü','Ù','Ú','Û','Ũ','Ū','Ŭ','Ů','Ű','Ų','Ư','Ǔ','Ǖ','Ǘ','Ǚ','Ǜ',
                    'ü','ù','ú','û','ũ','ū','ŭ','ů','ű','ų','ư','ǔ','ǖ','ǘ','ǚ','ǜ',
                    'Ŵ','ŵ',
                    'Ý','Ϋ','Ŷ','ý','ÿ','ŷ',
                    'Ź','Ż','Ž','ź','ż','ž',
                    'œ','Œ','æ','ǽ','Æ','Ǽ','Ĳ','ĳ');
    $replace = array('a','a','a','a','ae','a','a','a','a','a','a',
                     'a','a','a','a','ae','a',
                     'c','c','c','c','c',
                     'c','c','c','c','c',
                     'd','d','d','d','d','d',
                     'e','e','e','e','e','e','e','e','e',
                     'e','e','e','e','e','e','e','e','e',
                     'f','g','g','g','g',
                     'g','g','g','g',
                     'h','h','h','h',
                     'i','i','i','i','i','i','i','i','i','i',
                     'i','i','i','i','i','i','i','i','i','i',
                     'j','j',
                     'k','k',
                     'l','l','l','l','l',
                     'l','l','l','l','l',
                     'n','n','n','n',
                     'n','n','n','n','n',
                     'oe','o','o','o','o','o','o','o','o','o','o','o',
                     'oe','o','o','o','o','o','o','o','o','o','o','o','o',
                     'r','r','r','r','r','r',
                     's','s','s','s','ss',
                     's','s','s','s','s',
                     't','t','t','t','t','t',
                     'ue','u','u','u','u','u','u','u','u','u','u','u','u','u','u','u',
                     'ue','u','u','u','u','u','u','u','u','u','u','u','u','u','u','u',
                     'w','w',
                     'y','y','y','y','y','y',
                     'z','z','z','z','z','z',
                     'oe','oe','ae','ae','ae','ae','ij','ij');
    return strtolower(str_replace($search, $replace, $name));
}

/*
 * Global filter switcher, used in the different headers (mobile, cda...)
 */
function filters_switcher_link($mainFilterSwitchOn)
{
    $options_on = $options_off = array();
    $options_on['id'] = 'filter_switch_on';
    $options_off['id'] = 'filter_switch_off';

    if ($mainFilterSwitchOn)
    {
        $options_off['style'] = 'display: none;';
    }
    else
    {
        $options_on['style'] = 'display: none;';
    }

    $html = picto_tag('action_on', __('some filters active'), $options_on);
    $html .= picto_tag('action_off', __('some filters have been defined but are not activated'), $options_off);

    if (defined('PUN_ROOT'))
    {
        // we are in the forum
        // it is not possible to activate/disactivate filter because the FiltersSwitchFilter will not get executed.
        // moreover, forums are not filtered on activities, regions, langs.
        return $html;
    }
    else
    {
        return link_to_function($html, "$('#indicator').show();" .
          "$.ajax('" . url_for('@default?module=common&action=switchallfilters') . "')" .
            ".done(function() { $('#filter_switch_on, #filter_switch_off').toggle(); window.location.reload(); })" .
            ".always(function() { $('#indicator').hide(); })");
    }
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

function check_not_empty($value)
{
    return
    (   !empty($value)
     && !$value instanceof Doctrine_Null
     && !($value instanceof sfOutputEscaperObjectDecorator && $value->getRawValue() instanceof Doctrine_Null)
    );
}

function check_is_numeric($value)
{
    return
    (   !$value instanceof Doctrine_Null
     && !($value instanceof sfOutputEscaperObjectDecorator && $value->getRawValue() instanceof Doctrine_Null)
     && is_numeric($value)
     && ($value == (int)$value)
     && ($value >= 0)
    );
}

function check_is_numeric_or_text($value)
{
    return
    (   !$value instanceof Doctrine_Null
     && !($value instanceof sfOutputEscaperObjectDecorator && $value->getRawValue() instanceof Doctrine_Null)
     && (   (   is_numeric($value)
             && ($value == (int)$value)
             && ($value >= 0)
            )
         || !empty($value)
        )
    );
}

function check_is_positive($value)
{
    return
    (   !empty($value)
     && !$value instanceof Doctrine_Null
     && !($value instanceof sfOutputEscaperObjectDecorator && $value->getRawValue() instanceof Doctrine_Null)
     && is_numeric($value)
     && ($value == (int)$value)
     && ($value > 0)
    );
}

// check that the value from a single or multiple choice field is not empty
function check_list_not_empty($value, $multiple = false)
{
    if ($multiple)
    {
        return check_not_empty($value) && $value !== array("0") && $value !== array("");
    }
    else
    {
        return check_is_positive($value);
    }
}

// escaped doctrine null is not directly null, which is often annoying
function doctrine_value($value)
{
    return (check_is_numeric_or_text($value)) ? $value : null;
}

function _option(&$options, $name, $default = null, $remove = true)
{
    if (empty($options)) return $default;

    if (array_key_exists($name, $options))
    {
        $value = $options[$name];
        if ($remove)
        {
            unset($options[$name]);
        }
    }
    else
    {
        $value = $default;
    }

    return $value;
}
