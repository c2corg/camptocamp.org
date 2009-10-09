<?php
/**
 * Tools for document viewing
 * $Id: ViewerHelper.php 2202 2007-10-27 13:42:55Z alex $
 */

sfLoader::loadHelpers('Javascript');

function display_page_header($module, $document, $id, $metadata, $current_version, $prepend = '', $separator = ' : ', $nav_anchor_options = null)
{
    $is_archive = $document->isArchive();
    $content_class = $module . '_content';
    
    if ($prepend != '')
    {
        $prepend .=  $separator;
    }

    echo display_title($prepend . $document->get('name'), $module, true);

    echo '<div id="nav_space">&nbsp;</div>';

    // Navigation menus
    sfLoader::loadHelpers('WikiTabs');
    echo tabs_list_tag($id, $document->getCulture(), $document->isAvailable(), 'view',
                       $is_archive ? $document->getVersion() : NULL,
                       get_slug($document));
                                                                                        
    include_partial("$module/nav", array('id'  => $id, 'document' => $document));
    if ($nav_anchor_options == null)
    {
        include_partial("$module/nav_anchor");
    }
    else
    {
        include_partial("$module/nav_anchor", array('section_list' => $nav_anchor_options));
    }
    if ($module != 'users')
    {
        sfLoader::loadHelpers('Button');
        echo '<div id="nav_share">' . button_share() . '</div>';
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

function display_title($title_name = '', $module = null, $nav_status = true, $nav_status_pref = 'default_nav')
{
    $connected = true; //$this->getContext()->getUser()->isConnected();
    $js_var = init_js_var($nav_status, $nav_status_pref, $connected);
    
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
             . "\n" . '<h1 class="clearing"><span class="article_title_img '. $image. '"></span><span class="article_title">' . $title_name . '</span></h1>';
    }
    else
    {
        return $js_var
             . "\n" . '<span class="article_title">&nbsp;</span>';
    }
}

function display_content_top($wrapper_class = '')
{
    if (!empty($wrapper_class))
    {
        $wrapper_class = ' class="' . $wrapper_class . '"';
    }
    
    return '<div id="wrapper_context"' . $wrapper_class . '>
<div class="ombre_haut">
    <div class="ombre_haut_corner_right"></div>
    <div class="ombre_haut_corner_left"></div>
</div>';
}

function start_content_tag($content_class = '', $home = false)
{
    if (!empty($content_class))
    {
        $content_class = ' ' . $content_class;
    }

    $js_tag = javascript_tag($home ? 'setNav(true);' : 'setNav();'); // TODO to move smwhr else ?

    return '<div class="content_article"><div class="splitter" title="' . __('Reduce bar') . '"><div class="bar"></div></div>' .
           $js_tag . '<div class="article' . $content_class . '">';

}

function end_content_tag()
{
    return '</div></div>';
}
