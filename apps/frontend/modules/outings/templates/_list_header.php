<?php
echo header_list_tag('onam', 'name');
echo header_list_tag('date', 'date short');
echo header_list_tag('act', 'activities short');
//echo header_list_tag('alt', 'elevation short');
echo header_list_tag('hdif', 'height_diff_up short');
echo header_list_tag('cond', 'conditions_status');
echo header_list_tag('anam', 'region_name');
echo header_list_tag('geom', 'geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
echo simple_header_list_tag('author short');
