<?php
use_helper('General');

if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';
foreach ($associated_docs as $doc)
{
    $class = 'linked_elt';
    if (isset($doc['parent_id']) || (isset($doc['is_extra']) && $doc['is_extra']))
    {
        $class .= ' extra';
    }
    echo '<div class="' . $class . '">';
    echo ' ' . link_to(
                    ucfirst($doc['name']),
                    "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name'])
                      );
    if (is_scalar($doc['lowest_elevation']))
    {
        echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
    }
    else if (is_scalar($doc['elevation']))
    {
        echo '&nbsp; ' . $doc['elevation'] . __('meters');
    }
    if (isset($doc['scale']))
    {
        echo '&nbsp; (' . $doc['scale'] . ')';
    }
    if (isset($doc['public_transportation_types']))
    {
        echo '&nbsp; '. field_data_from_list($doc, 'public_transportation_types', 'app_parkings_public_transportation_types', true);
    }
    echo '</div>';
}
?>
</div>
</div>
<?php endif ?>
