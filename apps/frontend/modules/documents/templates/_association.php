<?php
use_helper('General', 'Field');

if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
if ($module == 'maps')
{
    $associated_docs = summarize_maps($associated_docs);
}

echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';
foreach ($associated_docs as $doc)
{
    $doc_id = $doc['id'];
    $class = 'linked_elt';
    if (isset($doc['parent_id']) || (isset($doc['is_extra']) && $doc['is_extra']))
    {
        $class .= ' extra';
    }
    echo '<div class="' . $class . '">';
    if ($module != 'users')
    {
        $name = ucfirst($doc['name']);
        $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']);
    }
    else
    {
        $name = $doc['name'];
        $url = "@document_by_id_lang?module=$module&id=$doc_id" . '&lang=' . $doc['culture'];
    }
    echo link_to($name, $url);
    if (isset($doc['lowest_elevation']) && $doc['lowest_elevation'] > 0)
    {
        echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
    }
    else if (is_scalar($doc['elevation']))
    {
        echo '&nbsp; ' . $doc['elevation'] . __('meters');
    }
    if (isset($doc['scale']) && !empty($doc['scale']))
    {
        echo '&nbsp; (' . $doc['scale'] . ')';
    }
    if (isset($doc['public_transportation_types']))
    {
        echo '&nbsp; '. field_data_from_list_if_set($doc, 'public_transportation_types', 'app_parkings_public_transportation_types', true, true);
    }
    echo '</div>';
}
?>
</div>
</div>
<?php endif ?>
