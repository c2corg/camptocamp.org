<?php 
$a = array();
foreach ($geoassociations as $geo_id => $geoP)
{
    $a[$geoP['type']] = link_to(ucfirst($geoP['AreaI18n'][0]['name']),
                                        "@document_by_id_lang?module=areas&id=$geo_id&lang=" . $geoP['AreaI18n'][0]['culture']);
}
krsort($a);
echo implode($a, ' '); 
?>
