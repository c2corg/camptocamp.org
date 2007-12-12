<?php
echo header_list_tag('name');
echo header_list_tag('elevation', 'elevation short');
echo header_list_tag('routes_quantity', 'routes_quantity short');
echo header_list_tag('site_types', 'type short');
echo header_list_tag('rock_types', 'rock_types short');
echo simple_header_list_tag('region_name');
echo header_list_tag('geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
