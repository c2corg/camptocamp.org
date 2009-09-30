<?php
use_helper('General', 'Field');

if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';

$is_inline = isset($inline);
if ($is_inline)
{
    echo '<div class="linked_elt">';
}
$is_first = true;

foreach ($associated_docs as $doc)
{
    $doc_id = $doc['id'];
    $class = 'linked_elt';
    if (isset($doc['parent_id']) || (isset($is_extra) && $is_extra))
    {
        $class .= ' extra';
    }
    if (!$is_inline)
    {
        echo '<div class="' . $class . '">';
    }
    elseif (!$is_first)
    {
        echo ', ';
        $is_first = false;
    }
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
        echo field_pt_picto_if_set($doc, true, true, ' - ');
    }
    if (!$is_inline)
    {
        echo '</div>';
    }
}
if ($is_inline)
{
    echo '</div>';
}

if (isset($extra_docs) && !empty($extra_docs))
{
    foreach ($extra_docs as $doc)
    {
        echo '<div class="linked_elt">' . $doc . '</div>';
    }
}
?>
</div>
</div>
<?php endif ?>
