<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('snam', 'name');
echo header_list_tag('salt', 'elevation short');
echo header_list_tag('styp', 'summit_type');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo picto_header_list_tag('picto_routes', 'nb_routes');
