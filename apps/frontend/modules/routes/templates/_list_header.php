<?php
use_helper('Field');
echo header_list_tag('name');
echo header_list_tag('activities', 'activities short');
echo header_list_tag('facing', 'facing short');
echo header_list_tag('height_diff_up', 'height_diff_up short');
echo simple_header_list_tag('ratings');
echo simple_header_list_tag('region_name');
echo header_list_tag('geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
