<?php
if (!isset($use_keys))
{
    $use_keys = false;
}

$at = array(
    'dc' => 'country',
    'dd' => 'admin_limits',
    'dr' => 'range',
    'dv' => 'valley_area');

foreach ($geoassociations as $geo_id => $geoP)
{
    $i18n = $geoP['AreaI18n'][0];
    $url = $use_keys ? array()
                     : array('url' => absolute_link(url_for("@document_by_id_lang_slug?module=areas&id=$geo_id&lang="
                                      . $i18n['culture'] . '&slug=' . make_slug($i18n['name'])))
                            );
    $a[$geoP['type'].$geo_id] = array_merge(array(
        'id' => $geo_id,
        'name' => $i18n->getRaw('name'),
        'type' => $at[$geoP['type']]
    ), $url);
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
