<?php
use_helper('General', 'Field');

if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';
foreach ($associated_docs as $doc)
{
    $doc_id = $doc['id'];
    $class = 'linked_elt';
    if (isset($doc['parent_id']) || (isset($is_extra) && $is_extra))
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
    if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']))
    {
        echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
    }
    else if (isset($doc['elevation']) && is_scalar($doc['elevation']))
    {
        echo '&nbsp; ' . $doc['elevation'] . __('meters');
    }
    if (isset($doc['public_transportation_types']))
    {
        echo field_data_from_list_if_set($doc, 'public_transportation_types', 'app_parkings_public_transportation_types', true, true, ' - ');
    }
    echo '</div>';
}
?>
</div>
</div>
<?php endif ?>
