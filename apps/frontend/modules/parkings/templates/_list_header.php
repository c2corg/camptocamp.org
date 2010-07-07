<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('pnam', 'name');
echo header_list_tag('palt', 'elevation short');
echo header_list_tag('tp', 'public_transportation_rating short');
echo header_list_tag('tpty', 'type short');
echo header_list_tag('scle', 'snow_clearance_rating');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo picto_header_list_tag('picto_routes', 'nb_routes');
