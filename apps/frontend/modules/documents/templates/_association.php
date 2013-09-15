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
$module_letter = c2cTools::Module2Letter($module);
$revert_ids = isset($type) ? (substr($type,0,1) != $module_letter) : null;
if (isset($ghost_module))
{
    $ghost_module_letter = c2cTools::Module2Letter($ghost_module);
    $revert_ghost_ids = isset($ghost_type) ? (substr($ghost_type,0,1) != $ghost_module_letter) : null;
}

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
    $reduce_name = (isset($reduce_name) && $reduce_name);
    $is_extra = (isset($is_extra) && $is_extra);
    $has_route_list_link = (isset($route_list_module) && !empty($route_list_ids) && !c2cTools::mobileVersion());

    if ($has_route_list_link)
    {
        $base_url = 'routes/list?';
        $param2 = "$route_list_module=$route_list_ids";
        $link_text = substr(__('routes'), 0, 1);
        $title = "routes linked to $module and $route_list_module";
    }

    foreach ($associated_docs as $doc)
    {
        $is_doc = (isset($doc['is_doc']) && $doc['is_doc']);
        $doc_id = $doc['id'];
        $idstring = isset($type) ? ' id="' . $type . '_' . ($revert_ids ? $id : $doc_id) . '"' : '';
        $class = 'linked_elt';
        $level = isset($doc['level']) ? $doc['level'] : 0;

        if ($level > 1)
        {
            $class .= ' level' . $level;
        }

        // unless required by the template, extra docs are the ones that are not directly linked to the
        // document, but shown as sub or super doc in a hierarchy
        if ((isset($doc['parent_relation']) && !isset($doc['link_tools']) && !$is_doc) || (isset($is_extra) && $is_extra))
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
            $name = $doc['name'];
            if ($level > 1 || $reduce_name)
            {
                if ($level > 1)
                {
                    $cut_level = 3;
                }
                else
                {
                    $cut_level = 2;
                }
                $name_list = explode(' - ', $name, $cut_level);
                $name = array_pop($name_list);
            }
            $name = ucfirst($name);
            if (!$is_doc)
            {
                $url = "@document_by_id_lang_slug?module=$module&id=$doc_id" . '&lang=' . $doc['culture'] . '&slug=' . make_slug($doc['name']);
            }
        }
        else
        {
            $name = $doc['name'];
            if (!$is_doc)
            {
                $url = "@document_by_id_lang?module=$module&id=$doc_id" . '&lang=' . $doc['culture'];
            }
        }

        echo $is_doc ? '<span class="current">' . $name . '</span>' : link_to($name, $url);

        // elevation info
        if (isset($doc['lowest_elevation']) && is_scalar($doc['lowest_elevation']) && $doc['lowest_elevation'] != $doc['elevation']) // for parkings
        {
            echo '&nbsp; ' . $doc['lowest_elevation'] . __('meters') . __('range separator') . $doc['elevation'] . __('meters');
        }
        else if (isset($doc['elevation']) && is_scalar($doc['elevation']))
        {
            echo '&nbsp; ' . $doc['elevation'] . __('meters');
        }

        // public transportation info
        if (isset($doc['public_transportation_types'])) // for parkings
        {
            echo field_pt_picto_if_set($doc, true, ' - ', '', false);
        }
        
        if ($has_route_list_link)
        {
            $param1 = "$module=$doc_id";
            if ($route_list_linked)
            {
                $url = $base_url . $param1 . '&' . $param2;
            }
            else
            {
                $url = $base_url . $param2 . '&' . $param1;
            }
            echo ' ' . link_to($link_text, $url,
                               array('title' => __($title),
                                     'class' => 'hide',
                                     'rel' => 'nofollow'));
        }

        // display tools for manipulating associations if user is moderator and displayed doc
        // is directly linked to current doc
        if ($show_link_to_delete && isset($doc['link_tools']))
        {
            $tips = (isset($doc['ghost_id']) && isset($ghost_module)) ? 'Delete the association with this ' . $module : null;
            
            echo c2c_link_to_delete_element($type, $revert_ids ? $id : $doc_id, $revert_ids ? $doc_id : $id,
                false, (int) $strict, null, 'indicator', $tips);
            
            if (isset($doc['ghost_id']) && isset($ghost_module))
            {
                $ghost_id = $doc['ghost_id'];
                $tips = 'Delete the association with this ' . $ghost_module;
                echo c2c_link_to_delete_element($ghost_type, $revert_ghost_ids ? $id : $ghost_id,
                    $revert_ghost_ids ? $ghost_id : $id, false, (int) $strict, null, 'indicator', $tips);
            
            }

            // button for changing a relation order
            if (in_array($type, array('ss', 'tt', 'pp')))
            {
                list($mi, $li) = ($doc['parent_relation'][$id] == 'parent') ?
                    array($id, $doc_id) : array($doc_id, $id);

                echo link_to(image_tag(sfConfig::get('app_static_url') . '/static/images/picto/move.gif'),
                    "@default?module=documents&action=invertAssociation&type=$type&main_id=$mi&linked_id=$li");
            }
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
