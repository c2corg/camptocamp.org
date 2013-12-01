<?php
/**
 * Tools for document viewing
 * $Id: ViewerHelper.php 2202 2007-10-27 13:42:55Z alex $
 */

sfLoader::loadHelpers('Javascript', 'General');

function display_page_header($module, $document, $id, $metadata, $current_version, $options = array())
{
    $is_archive = $document->isArchive();
    $mobile_version = c2cTools::mobileVersion();
    $content_class = $module . '_content';
    $lang = $document->getCulture();
    $version = ($is_archive ? $document->getVersion() : NULL);
    $slug = '';
    $prepend = _option($options, 'prepend', '');
    $separator = _option($options, 'separator', '');
    $nav_options = _option($options, 'nav_options');
    $item_type = _option($options, 'item_type', '');
    $nb_comments = _option($options, 'nb_comments');
    $creator_id = _option($options, 'creator_id');

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
    
    if (!empty($prepend))
    {
        $prepend .=  $separator;
    }

    echo display_title($prepend . $document->get('name'), $module, true, 'default_nav', $url);

    if (!$mobile_version) // left navigation menus are only for web version
    {
        echo '<div id="nav_space">&nbsp;</div>';

        sfLoader::loadHelpers('WikiTabs');
        
        $tabs = tabs_list_tag($id, $lang, $document->isAvailable(), 'view', $version, $slug, $nb_comments);
        
        echo $tabs;

        // liens internes vers les sections repliables du document
        if ($nav_options == null)
        {
            include_partial("$module/nav_anchor");
        }
        else
        {
            include_partial("$module/nav_anchor", array('section_list' => $nav_options));
        }
        
        // boutons vers des fonctions annexes et de gestion du document
        include_partial("$module/nav", isset($creator_id) ? 
                                       array('id'  => $id, 'document' => $document, 'creator_id' => $creator_id) :
                                       array('id'  => $id, 'document' => $document));
        
        if ($module != 'users')
        {
            sfLoader::loadHelpers('Button');
            echo '<div id="nav_share" class="nav_box">' . button_share() . '</div>';
        }
    }

    echo display_content_top('doc_content', $item_type);

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

function init_js_var($default_nav_status = true, $nav_status_pref = 'default_nav')
{
    $vars = array('default_nav_status' => ($default_nav_status) ? 'true' : 'false',
                  'confirm_msg' =>  __('Are you sure?'),
                  'section_open' => __('section open'),
                  'section close' => __('section close'),
                  'nav_status_string' => $nav_status_pref,
                  'nav_status_cookie_position' => array_search($nav_status_pref, sfConfig::get('app_personalization_cookie_fold_positions')));

    $js = '(function(C2C){';
    foreach ($vars as $var => $value) {
      $js .= "C2C.$var = " . (is_int($value) ? $value : "'$value'") . ";";
    }
    $js .= '})(window.C2C = window.C2C || {});';

    return javascript_tag($js);
}

function display_title($title_name = '', $module = null, $nav_status = true, $nav_status_pref = 'default_nav', $url = '')
{
    $js_var = init_js_var($nav_status, $nav_status_pref);
    
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

function display_content_top($wrapper_class = '', $item_type = '')
{
    $mobile_version = c2cTools::mobileVersion();
    
    if (!empty($wrapper_class))
    {
        $wrapper_class = ' class="' . $wrapper_class . '"';
    }

    //schema.org item type
    $itemscope = empty($item_type) ? '' : ' itemscope itemtype="' . $item_type . '"';

    return '<div id="wrapper_context"' . $wrapper_class . $itemscope . '>' .
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

    $js_tag = javascript_tag("C2C.setSectionStatus('nav', C2C.nav_status_cookie_position, C2C.default_nav_status)"); // TODO move smwhr else?

    return '<div class="content_article">' . (!$mobile_version ? '<div id="splitter" data-title-reduce="' . __('Reduce the bar') .
           '" data-title-enlarge="' . __('Enlarge the bar') . '"></div>' . $js_tag : '') . '<article class="article' . $content_class . '">';

}

function end_content_tag()
{
    return '</article></div>';
}
