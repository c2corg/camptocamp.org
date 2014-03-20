<?php
$at = array(
    'dc' => 'country',
    'dd' => 'admin_limits',
    'dr' => 'range',
    'dv' => 'valley_area');

foreach ($geoassociations as $geo_id => $geoP)
{
    $i18n = $geoP['AreaI18n'][0];
    $a[$geoP['type'].$geo_id] = array(
        'name' => $i18n->getRaw('name'),
        'url' => absolute_link(url_for("@document_by_id_lang_slug?module=areas&id=$geo_id&lang="
                     .$i18n['culture'].'&slug='.make_slug($i18n['name']))),
        'type' => $at[$geoP['type']]
    );
}

if (isset($a))
{
    krsort($a);
    echo json_encode(array_values($a));
}
else
{
    echo '[]';
}
