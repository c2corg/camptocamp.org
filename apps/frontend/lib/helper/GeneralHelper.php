<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($search_name)
{
    // FIXME: quotes (') are replaced by &#039; in retrieved search_name fields!?
    $slug = str_replace(array(' ', '&#039;', '(', ')', '/', ',', '>', ':', '?', '!', "'", '_'),
                        '-', $search_name);
    $slug = explode('-', $slug);
    $slug = array_filter($slug, '_keep_in_slug');
    $slug = implode('-', $slug);
    return substr($slug, 0, 100);
}

function get_slug($document)
{
    return formate_slug($document->get('search_name'));
}

function _keep_in_slug($str)
{
    return !empty($str);
}
