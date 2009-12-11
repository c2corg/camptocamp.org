<?php 
$a = array();
foreach ($geoassociations as $geo_id => $geoP)
{
    $i18n = $geoP['AreaI18n'][0];
    $a[$geoP['type'].$geo_id] = link_to(ucfirst($i18n['name']),
                                        "@document_by_id_lang_slug?module=areas&id=$geo_id&lang=" . $i18n['culture'] . '&slug=' . make_slug($i18n['name']));
}
krsort($a);
echo implode($a, ' ');
?>
