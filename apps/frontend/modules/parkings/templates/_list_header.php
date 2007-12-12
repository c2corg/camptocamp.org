<?php
echo header_list_tag('name');
echo header_list_tag('elevation', 'elevation short');
echo header_list_tag('public_transportation_rating', 'public_transportation_rating short');
echo simple_header_list_tag('region_name');
echo header_list_tag('geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
