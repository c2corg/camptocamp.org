<?php 
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('unam', 'name');
echo header_list_tag('fnam', 'nick_name');
echo header_list_tag('cat', 'category');
echo header_list_tag('act', 'activities short');
echo header_list_tag('anam', 'region_name'); 
echo images_header_list_tag();
echo comments_header_list_tag();
