<?php
use_helper('Field');

$activities = field_activities_data($data, true);
$ratings = field_route_ratings_data($data);
$facing = _get_field_value_in_list(sfConfig::get('app_routes_facings'), $data->get('facing'));
$height = $data->get('height_diff_up');
if ($height)
{
    $height .= ' ' . __('meters');
}

$data = array();
if ($activities) $data[] = $activities;
if ($facing) $data[] = $facing;
if ($height) $data[] = $height;
if ($ratings) $data[] = $ratings;

echo implode(' - ', $data);
