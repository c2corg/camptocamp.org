<?php
use_helper('Field');

$doc_id = $data->get('id');

$ratings = field_route_ratings_data($data);
$facing = _get_field_value_in_list(sfConfig::get('app_routes_facings'), $data->get('facing'));
$height = $data->get('height_diff_up');
if ($height)
{
    $height .= ' ' . __('meters');
}

$data = array();
if ($facing) $data[] = $facing;
if ($height) $data[] = $height;
if ($ratings) $data[] = $ratings;

$link = link_to(__('Show the route'), '@document_by_id?module=routes&id='.$doc_id,
                array('onclick' => "window.open(this.href);return false;"));

echo __('Short description: '), implode(' - ', $data), ' (', $link, '</a>)';
