<?php
/**
 * Tools for document viewing
 * $Id: ViewerHelper.php 2202 2007-10-27 13:42:55Z alex $
 */

function display_page_header($module, $document, $id, $metadata, $current_version, $prepend = '')
{
    $is_archive = $document->isArchive();
    
    if ($prepend != '')
    {
        $prepend .=  ' : ';
    }

    if ($module == 'users' && !$is_archive)
    {
        echo display_title($document->get('private_data')->getSelectedName(), $module);
    }
    else
    {
        echo display_title($prepend . $document->get('name'), $module);
    }

    echo '<div id="nav_space">&nbsp;</div>';

    // Navigation menus
    use_helper('WikiTabs');
    echo tabs_list_tag($id, $document->getCulture(), $document->isAvailable(), 'view',
                       $is_archive ? $document->getVersion() : NULL);
                                                                                        
    include_partial("$module/nav", array('id'  => $id, 'document' => $document));
    include_partial("$module/nav_anchor");

    echo '<div id="wrapper_context">
            <div id="ombre_haut">
              <div id="ombre_haut_corner_right"></div>
              <div id="ombre_haut_corner_left"></div>
            </div>
          <div id="content_article">
          <div id="article">';

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

function display_title($title_name, $module=null)
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
