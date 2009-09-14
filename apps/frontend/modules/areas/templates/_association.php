<?php
use_helper('General', 'Field');

if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
$area_type_list = array_keys(sfConfig::get('app_areas_area_types'));
array_shift($area_type_list);
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';
foreach ($area_type_list as $area_type)
{
    $element = array();
    foreach ($associated_docs as $doc)
    {
        if ($doc['area_type'] != $area_type)
        {
            continue;
        }
        $doc_id = $doc['id'];
        $name = ucfirst($doc['name']);
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']);
        $element[] = link_to($name, $url);
    }
    if (!empty($element))
    {
        echo '<div class="linked_elt">' . implode(', ', $element) . '</div>';
    }
}
?>
</div>
</div>
<?php endif ?>
