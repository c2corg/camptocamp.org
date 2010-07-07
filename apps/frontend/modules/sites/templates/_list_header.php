<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('tnam', 'name');
echo header_list_tag('talt', 'elevation short');
echo header_list_tag('rqty', 'routes_quantity short');
echo header_list_tag('ttyp', 'type short');
echo header_list_tag('trock', 'rock_types short');
echo simple_header_list_tag('parkings');
echo header_list_tag('anam', 'region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
echo picto_header_list_tag('picto_outings', 'nb_outings');
