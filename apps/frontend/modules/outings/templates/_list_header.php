<?php
use_helper('Field');

$request = sfContext::getInstance()->getRequest();
$orderby = $request->getParameter('orderby');

if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('onam', 'name');
echo header_list_tag('date', 'date short', 'desc');
echo header_list_tag('act', 'activities short');
echo header_list_tag('alt', 'elevation short');
echo header_list_tag('hdif', 'height_diff_up short');
if (!empty($orderby) && in_array($orderby, sfConfig::get('mod_outings_sort_route_criteria')))
{
    echo header_list_tag($orderby, 'ratings');
}
else
{
    echo simple_header_list_tag('ratings');
}
echo header_list_tag('cond', 'cond short');
echo simple_header_list_tag('frequentation short');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo simple_header_list_tag('author short');

