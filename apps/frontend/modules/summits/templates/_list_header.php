<?php
echo header_list_tag('name');
echo header_list_tag('elevation', 'elevation short');
echo header_list_tag('summit_type');
echo simple_header_list_tag('region_name'); // no filtering possible on this item because multiple.
echo header_list_tag('geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
