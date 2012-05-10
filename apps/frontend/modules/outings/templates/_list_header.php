<?php
use_helper('Field', 'Pagination');

$params_list = array_keys(c2cTools::getCriteriaRequestParameters());
$is_default_list = empty($params_list);

$request = sfContext::getInstance()->getRequest();
$orderby = $request->getParameter('orderby');
$is_orderby_rating = (!empty($orderby) && in_array($orderby, sfConfig::get('mod_outings_sort_route_criteria')));


echo select_all_header_list_tag();
echo header_list_tag('onam', 'name', '', $is_default_list);
echo header_list_tag('date', 'date short', 'desc');
echo header_list_tag('act', 'activities short', '', $is_default_list);
echo header_list_tag('alt', 'elevation short');
echo header_list_tag('hdif', 'height_diff_up short');
echo header_list_tag($orderby, 'ratings', '', !$is_orderby_rating);
echo header_list_tag('cond', 'cond short');
echo simple_header_list_tag('frequentation short');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo simple_header_list_tag('author short');

