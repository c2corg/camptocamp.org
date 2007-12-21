<?php
use_helper('Field');
echo header_list_tag('rnam', 'name');
echo header_list_tag('act', 'activities short');
echo header_list_tag('fac', 'facing short');
echo header_list_tag('hdif', 'height_diff_up short');
echo simple_header_list_tag('ratings');
echo header_list_tag('anam', 'region_name');
echo header_list_tag('geom', 'geom_wkt');
echo images_header_list_tag();
echo comments_header_list_tag();
