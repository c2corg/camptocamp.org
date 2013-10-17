<?php
use_helper('Text', 'Field', 'sfBBCode', 'SmartFormat', 'MyImage', 'General', 'Javascript', 'MyMinify');

function truncate_description($description, $route, $length = 500, $has_abstract = false) {
    if ($has_abstract)
    {
        $description = extract_abstract($description);
    }
    $more = '... <span class="more_text">' . link_to('[' . __('Read more') . ']', $route) . '</span>';
    return parse_links(parse_bbcode_simple(truncate_text($description, $length, $more)));
}

function make_popup_title($title, $module, $route = null) {
    if (!empty($route)) {
        $title = link_to($title, $route,
                         array('target' => '_blank',
                               'title'  => __('Show document')));
    }
    return '<h3>'
         . '<span class="article_title_img img_title_' . $module . '"></span>'
         . $title
         . '</h3>';
}

function make_thumbnail_slideshow($images) {
    if (!count($images)) return '';

    $output = '<div class="popup_slideshow"><ul class="popup_slideimages">';

    $count = 0;
    foreach($images as $image) {
        $count += 1;
        $caption = $image['name'];
        $class = ($count != 1) ? '' : ' class="popup-img-active"';
        $output .= "<li$class>" . image_tag(image_url($image['filename'], 'medium'),
                                            array('alt' => $caption, 'title' => $caption))
                 . '</li>';
    }

    $output .= '</ul></div>';

    return $output;
}

function insert_popup_js()
{
    return '<script type="text/javascript" src="' .
        minify_get_combined_files_url(array('/static/js/jquery.min.js', '/static/js/popup.js')) .
        '"></script>';
}

function make_routes_title($title, $nb_routes)
{
    $output = '<h4 class="popup_list_title">';
    
    if ($nb_routes)
    {
        $output .= $title . __('&nbsp;:') . ' ' . $nb_routes;
    }
    else
    {
        $output .= __('No linked route');
    }
    
    $output .= '</h4>';
    
    return $output;
}
