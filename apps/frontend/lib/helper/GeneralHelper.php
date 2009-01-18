<?php
/**
 * Helper containing globally used tools
 */

function formate_slug($search_name)
{
    $slug = str_replace('#039;', '-', $search_name);
    $slug = html_entity_decode($slug);
    $slug = preg_replace('/[\W\s_]/', '-', $slug);
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
    return !is_null($str) && $str != '';
}
