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
    return language_select_list(Language::getAll(), 'culture_selection', 'lang',
                                sfContext::getInstance()->getUser()->getCulture(),
                                '@switch_culture_interface');
    
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


function language_select_list($languages, $id, $url_parameter, $current_language, $action = false, 
                              $translated_languages = null)
{
    if (!$action)
    {
        // get current url
        $current_uri = sfRouting::getInstance()->getCurrentInternalUri();
    }

    $i = 1;
    $lang_nb = count($languages);
    $languages_list = '';
    foreach($languages as $language => $value)
    {
        if (!$action)
        {
    	    $new_uri = preg_replace("/$url_parameter=([a-z][a-z])/", "$url_parameter=$language",
                                    $current_uri, -1, $count);

            if ($count <= 0)
            {
                // no culture was found, we set it manually
                // check if there are parameters in the URL
   	            $start = strpos($new_uri, '?') ? '&' : '?';
                $new_uri .= "$start$url_parameter=$language";
            }
        }
        else
        {
            $new_uri = "$action?$url_parameter=$language";
    	}

    	if ($translated_languages)
    	{
            $link = format_language_c2c($language);
            if ($current_language == $language)
            {
                $link = '<div class="current_lang">' . $link . '</div>';
            }
            else
            {
                $options = in_array($language, $translated_languages) ? 
                           array('class' => 'translated') :
                           array('class' => 'not_translated');

                $link = link_to($link, $new_uri, $options);
            }

            if ($i++ < $lang_nb)
            {
                $link .= '&nbsp;|&nbsp;';
            }

    	}
    	else
    	{
            if ($current_language == $language)
            {
                $link = '<strong>' . $language . '</strong>';
            }
            else
            {
                $link = link_to($language, $new_uri);
            }

            if ($i++ < $lang_nb)
            {
                $link .= '&nbsp;|&nbsp;';
    	    }
        }

        $languages_list .= $link;
    }

    return ' ' . $languages_list;
}
