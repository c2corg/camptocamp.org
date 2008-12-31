<?php
/**
 * Languages selectors tools
 * @version $Id: LanguageHelper.php 1244 2007-08-10 15:27:59Z jbaubort $
 */

use_helper('Form');


/**
* Replace the format_language helper from symfony
* because it is too slow
*/
function format_language_c2c($language_code)
{
    // get c2c languages
    $c2c_languages = Language::getAll();
    
    // little test to be sur that the language_code exists
    if(!in_array($language_code, array_keys($c2c_languages)))
    {
        throw new exception("{languageHelper}{format_language_c2c} bad language code");
    }
    
    // returns the human version
    return __($c2c_languages[$language_code]);
}

/**
 * Pay attention that there is a select_language_tag() existing in FormHelper
 */
function select_language_c2c_tag()
{
    return select_tag('lang',
                      options_for_select(Language::getAll(),
                                         sfContext::getInstance()->getUser()->getCulture()));
}

function select_interface_language()
{
    $items = array();
    $current_language = sfContext::getInstance()->getUser()->getCulture();
    foreach (Language::getAll() as $language => $value)
    {
        if ($current_language == $language)
        {
            $items[] = '<strong>' . $language . '</strong>';
        }
        else
        {
            $items[] = link_to($language, "@switch_culture_interface?lang=$language",
                               array('title' => $value));
        }
    }

    return implode('&nbsp;|&nbsp;', $items);

    // Old version with dropdown list
    /*
    return form_tag('user/setCulture') .
           select_tag('sf_culture',
                      options_for_select(Language::getAll(),
                                         sfContext::getInstance()->getUser()->getCulture()),
                                         array('onChange' => 'javascript:submit(this);return false;')) .
           '</form>';
    */
}

function language_select_list($module, $id, $current_language, $translated_languages)
{
    $items = array();
    foreach (Language::getAll() as $language => $value)
    {
        $lang = format_language_c2c($language);
        if ($current_language == $language)
        {
            $items[] = '<div class="current_lang">' . $lang . '</div>';
        }
        else
        {
            $options = in_array($language, $translated_languages) ?
                       array('class' => 'translated') :
                       array('class' => 'not_translated');
            $items[] = link_to($lang, "@document_by_id_lang?module=$module&id=$id&lang=$language", $options);
        }
    }

    return implode('&nbsp;|&nbsp;', $items);
}
