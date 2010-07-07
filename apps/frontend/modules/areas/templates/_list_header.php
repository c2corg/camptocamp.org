<?php
if (!c2cTools::mobileVersion()) echo select_all_header_list_tag();
echo header_list_tag('anam', 'name');
echo header_list_tag('atyp', 'area_type');
echo images_header_list_tag();
echo comments_header_list_tag();
