<?php 
echo select_all_header_list_tag();
echo header_list_tag('unam', 'name');
echo header_list_tag('fnam', 'nick_name');
if (in_array('mail', $custom_fields))
{
    echo simple_header_list_tag('Email short');
}
echo header_list_tag('cat', 'category');
echo header_list_tag('act', 'activities short');
echo region_header_list_tag('region_name'); 
echo images_header_list_tag();
echo comments_header_list_tag();
