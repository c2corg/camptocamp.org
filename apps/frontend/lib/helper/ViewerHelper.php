<?php
/**
 * Tools for document viewing
 * $Id: ViewerHelper.php 2202 2007-10-27 13:42:55Z alex $
 */

sfLoader::loadHelpers('Javascript');

function display_page_header($module, $document, $id, $metadata, $current_version, $prepend = '', $separator = ' : ', $nav_options = null)
{
    $is_archive = $document->isArchive();
    $mobile_version = c2cTools::mobileVersion();
    $content_class = $module . '_content';
    $lang = $document->getCulture();
    $version = ($is_archive ? $document->getVersion() : NULL);
    $slug = '';
    
    if (!$is_archive)
    {
        if ($module != 'users')
        {
            $slug = get_slug($document);
            $url = "@document_by_id_lang_slug?module=$module&id=$id&lang=$lang&slug=$slug";
        }
        else
        {
            $url = "@document_by_id_lang?module=$module&id=$id&lang=$lang";
        }
    }
    else
    {
        $url = "@document_by_id_lang_version?module=$module&id=$id&lang=$lang&version=$version";
    }
    
    if ($prepend != '')
    {
        $prepend .=  $separator;
    }

    echo display_title($prepend . $document->get('name'), $module, true, 'default_nav', $url);

    if (!$mobile_version) // left navigation menus are only for web version
    {
        echo '<div id="nav_space">&nbsp;</div>';

        sfLoader::loadHelpers('WikiTabs');
        
        $tabs = tabs_list_tag($id, $lang, $document->isAvailable(), 'view', $version, $slug);
        
        echo $tabs;

        include_partial("$module/nav", array('id'  => $id, 'document' => $document));
        if ($nav_options == null)
        {
            include_partial("$module/nav_anchor");
        }
        else
        {
            include_partial("$module/nav_anchor", array('section_list' => $nav_options));
        }
        
        if ($module != 'users')
        {
            sfLoader::loadHelpers('Button');
            echo '<div id="nav_share" class="nav_box">' . button_share() . '</div>';
        }
    }

    echo display_content_top('doc_content');

    echo start_content_tag($content_class);

    if ($merged_into = $document->get('redirects_to'))
    {
        include_partial('documents/merged_warning', array('merged_into' => $merged_into));
    }

    if ($is_archive)
    {
        include_partial('documents/versions_browser', array('id'      => $id,
                                                            'document' => $document,
                                                            'metadata' => $metadata,
                                                            'current_version' => $current_version));
    }
}

function init_js_var($default_nav_status = true, $nav_status_pref = 'default_nav', $connected = false)
{
    $default_nav_status = ($default_nav_status) ? 'true' : 'false';
    $connected_string = ($connected) ? "\n" . 'confirm_msg = \'' . __('Are you sure?') . '\';' : '';
    $nav_status_cookie_position = array_search($nav_status_pref, sfConfig::get('app_personalization_cookie_fold_positions'));
    $js_var = javascript_tag('open_close = Array(\''.__('section open').'\', \''.__('section close').'\', \''.__('Enlarge the bar').
                             '\', \''.__('Reduce the bar')."');\n" . 'default_nav_status = ' . $default_nav_status . ';' . $connected_string .
                             'var nav_status_string = \''.$nav_status_pref.'\';var nav_status_cookie_position='.$nav_status_cookie_position);
    return $js_var;
}

function display_title($title_name = '', $module = null, $nav_status = true, $nav_status_pref = 'default_nav', $url = '')
{
    $connected = true; //$this->getContext()->getUser()->isConnected();
    $js_var = init_js_var($nav_status, $nav_status_pref, $connected);
    
    if (!empty($url))
    {
        $title_name = link_to($title_name, $url);
    }
    
    if(!empty($title_name))
    {
        if($module)
        {
            $image = ' img_title_' . $module;
        }
        else
        {
            $image = 'img_title_noimage';
        }
        return $js_var
             . "\n" . '<h1 class="clearing"><span class="article_title_img '. $image. '"></span><span id="article_title" class="article_title">' . $title_name . '</span></h1>';
    }
    else
    {
        return $js_var
             . "\n" . '<span id="article_title" class="article_title">&nbsp;</span>';
    }
}

function display_content_top($wrapper_class = '')
{
    $mobile_version = c2cTools::mobileVersion();
    
    if (!empty($wrapper_class))
    {
        $wrapper_class = ' class="' . $wrapper_class . '"';
    }

    return '<div id="wrapper_context"' . $wrapper_class . '>' .
           (!$mobile_version ? '<div class="ombre_haut"><div class="ombre_haut_corner_right"></div>' .
                               '<div class="ombre_haut_corner_left"></div></div>' : '');
}

function start_content_tag($content_class = '', $home = false)
{
    $mobile_version = c2cTools::mobileVersion();

    if (!empty($content_class))
    {
        $content_class = ' ' . $content_class;
    }

    $js_tag = javascript_tag($home ? 'setNav(true);' : 'setNav();'); // TODO to move smwhr else ?

    return '<div class="content_article">' . (!$mobile_version ? '<div id="splitter" title="' . __('Reduce the bar') .
           '"></div>' . $js_tag : '') . '<article class="article' . $content_class . '">';

}

function end_content_tag()
{
    return '</article></div>';
}
