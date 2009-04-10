<?php
use_helper('FilterForm');

echo javascript_tag(
'field_list = new Array(\'salt\', \'halt\', \'palt\', \'hdif\', \'ralt\', \'dhei\', \'fac\', \'days\', \'grat\', \'erat\', \'prat\', \'frat\', \'rrat\', \'arat\', \'irat\', \'mrat\', \'trat\', \'expo\', \'lrat\', \'srat\', \'hrat\', \'rlen\');
optionIndex_list = new Array(3, 3, 3, 3, 3, 3, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
focus_field = \'rnam\';'
);

include_partial('areas/areas_selector', array('ranges' => $ranges));
include_partial('summits/summits_short_filter');
include_partial('huts/huts_short_filter');
include_partial('parkings/parkings_filter');
include_partial('routes_filter');
echo '<br />' . georef_selector() . '<br />';
include_partial('documents/filter_sort');
