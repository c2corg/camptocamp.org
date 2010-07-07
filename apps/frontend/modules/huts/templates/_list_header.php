<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('hnam', 'name');
echo header_list_tag('halt', 'elevation short');
echo header_list_tag('styp', 'type short');
echo header_list_tag('hscap', 'staffed_capacity short');
echo header_list_tag('hucap', 'unstaffed_capacity short');
echo header_list_tag('act', 'activities short');
echo simple_header_list_tag('phone short');
echo simple_header_list_tag('www');
echo simple_header_list_tag('parkings');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo picto_header_list_tag('picto_routes', 'nb_routes');
