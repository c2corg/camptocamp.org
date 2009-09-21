<?php
/**
 * Tools for document viewing
 * $Id: ViewerHelper.php 2202 2007-10-27 13:42:55Z alex $
 */

use_helper('Javascript');

function display_page_header($module, $document, $id, $metadata, $current_version, $prepend = '', $separator = ' : ', $nav_anchor_options = null)
{
    $is_archive = $document->isArchive();
    $content_class = $module . '_content';
    
    if ($prepend != '')
    {
        $prepend .=  $separator;
    }

    echo javascript_tag('open_close = Array(\''.__('section open').'\', \''.__('section close').'\', \''.__('Show bar').'\', \''.__('Reduce bar').'\')');
    
    echo display_title($prepend . $document->get('name'), $module);

    echo '<div id="nav_space">&nbsp;</div>';

    // Navigation menus
    use_helper('WikiTabs');
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
        use_helper('Button');
        echo '<div id="nav_share">' . button_share() . '</div>';
    }

    echo display_content_top();

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

function display_title($title_name = '', $module=null)
{
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
        return '<div class="clearing"><span class="article_title_img '. $image. '"></span><span class="article_title">' . $title_name . '</span></div>';
    }
    else
    {
        return '<span class="article_title">&nbsp;</span>';
    }
}

function display_content_top($wrapper_class = '')
{
    if (!empty($wrapper_class))
    {
        $wrapper_class = ' class="' . $wrapper_class . '"';
    }
    
    return '<div id="wrapper_context"' . $wrapper_class . '>
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>';
}

function start_content_tag($content_class = '')
{
    if (!empty($content_class))
    {
        $content_class = ' ' . $content_class;
    }
    
    return '<table class="content_article"><tbody><tr>
    <td class="splitter" title="' . __('Reduce bar') . '"></td>
    <td class="article' . $content_class . '">';
}

function end_content_tag()
{
    return '    </td>'
         . '</tr></tbody></table>';
}
