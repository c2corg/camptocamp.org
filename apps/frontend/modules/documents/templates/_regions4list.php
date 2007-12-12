<?php 
$a = array();
foreach ($geoassociations as $geo_id => $geoP)
{
    $a[$geoP['type']] = link_to(ucfirst($geoP['AreaI18n'][0]['name']), "@document_by_id?module=areas&id=$geo_id");
}
krsort($a);
echo implode($a, ' '); 
?>