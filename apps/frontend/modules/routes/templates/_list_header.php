<?php
use_helper('Field');

$request = sfContext::getInstance()->getRequest();
$orderby = $request->getParameter('orderby', '');
$add_rating_link = (!empty($orderby) && in_array($orderby, sfConfig::get('mod_routes_sort_route_criteria')));

if (!$add_rating_link and !empty($activities))
{
    $orderby = Route::getDefaultRatingOrderby($activities);
    $add_rating_link = (!empty($orderby));
}

echo select_all_header_list_tag();
echo header_list_tag('rnam', 'name');
echo header_list_tag('act', 'activities short');
echo header_list_tag('maxa', 'elevation short');
echo header_list_tag('fac', 'facing short');
echo header_list_tag('hdif', 'height_diff_up short');
echo header_list_tag($orderby, 'ratings', '', !$add_rating_link);
echo simple_header_list_tag('parkings');
echo region_header_list_tag('region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo picto_header_list_tag('picto_outings', 'nb_linked_outings');
