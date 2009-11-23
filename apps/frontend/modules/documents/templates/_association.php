<?php
use_helper('AutoComplete', 'General', 'Field');

$has_associated_docs = count($associated_docs);
$has_extra_docs = (isset($extra_docs) && check_not_empty($extra_docs));
if (isset($document))
{
    $id = $document->get('id');
}
if (!isset($show_link_to_delete))
{
    $show_link_to_delete = false;
}
// correctly set main_id and linked_id
$revert_ids = isset($type) ? ($type[0] != c2cTools::Module2Letter($module)) : null;

if ($has_associated_docs || $has_extra_docs): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).__('&nbsp;:').'</span></div>';

if ($has_associated_docs)
{
    $is_inline = isset($inline); //case for users list in outings
    $has_merge_inline = isset($merge_inline) && trim($merge_inline) != '';
    if ($is_inline)
    {
        echo '<div class="linked_elt">';
    }
    $is_first = true;

    foreach ($associated_docs as $doc)
    {
        $doc_id = $doc['id'];
        $idstring = isset($type) ? ' id="' . $type . '_' . ($revert_ids ? $id : $doc_id) . '"' : '';
        $class = 'linked_elt';

        if (isset($doc['is_child']) and $doc['is_child'])
        {
            $class .= ' child';
        }

        if (isset($doc['parent_id']) || (isset($is_extra) && $is_extra))
        {
            $class .= ' extra';
        }

        if (!$is_inline)
        {
            echo '<div class="' . $class . '"' . $idstring . '>';
        }
        else
        {
            echo '<span' . $idstring . '>';
            if (!$is_first)
            {
                echo ', ';
            }
        }
        $is_first = false;
        
        if ($module != 'users')
        {
            $name = ucfirst($doc['name']);
            $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']);
        }
        else
        {
            $name = $doc['name'];
            $url = "@document_by_id_lang?module=$module&id=$doc_id" . '&lang=' . $doc['culture'];
        }

        echo link_to($name, $url);

        if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']) && $doc['lowest_elevation'] != $doc['elevation'])
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

        if (!isset($doc['parent_id']) and $show_link_to_delete)
        {
            echo c2c_link_to_delete_element($type, $revert_ids ? $id : $doc_id, $revert_ids ? $doc_id : $id, false, (int)$strict);
        }

        echo $is_inline ? '</span>' : '</div>';
    }
    if ($is_inline)
    {
        if ($has_merge_inline)
        {
            echo ', ' . $sf_data->getRaw('merge_inline');
        }
        echo '</div>';
    }
}

if ($has_extra_docs)
{
    $extra_docs_raw = $sf_data->getRaw('extra_docs');
    foreach ($extra_docs_raw as $doc)
    {
        if (!empty($doc))
        {
            echo '<div class="linked_elt">' . $doc . '</div>';
        }
    }
}
?>
</div>
</div>
<?php endif ?>
