<?php
echo select_all_header_list_tag();
echo header_list_tag('xnam', 'name');
echo header_list_tag('date', 'date short', 'desc');
echo header_list_tag('act', 'activities short');
echo header_list_tag('xalt', 'elevation short');
echo header_list_tag('xtyp', 'event_type short');
echo header_list_tag('xsev', 'severity short');
echo header_list_tag('ximp', 'nb_impacted short');
echo region_header_list_tag('region_name');
echo images_header_list_tag();
echo comments_header_list_tag();
