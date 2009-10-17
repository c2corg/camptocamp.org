<?php 
use_helper('AutoComplete', 'Field', 'General');

if (!isset($show_link_to_delete))
{
    $show_link_to_delete = false;
}

if (count($associated_docs)):
?>

<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img picto_'.$module.'" title="'.ucfirst(__($module)).'">';
if (!isset($title))
{
    echo '<span>'.ucfirst(__($module)).__('&nbsp;:').'</span>';
}
echo '</div>';
if (isset($title))
{
    $print = (count($associated_docs)) ? '' : ' no_print';
    echo '<div id="_' . $title . '" class="section_subtitle' . $print . '">' . __($title) . '</div>';
}
foreach ($associated_docs as $doc): ?>
    <div class="linked_elt">
        <?php
        echo link_to($doc['name'], "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name']));
        if (isset($display_info) && $display_info)
        {
            echo summarize_route($doc, true, true);
        }
        if (!isset($doc['parent_id']) and $show_link_to_delete)
        {
            echo c2c_link_to_delete_element($type, $doc_id, $id, false, $strict);
        }
        ?>
    </div>
<?php endforeach; ?>

</div>
</div> <!-- one_kind_association -->

<?php endif ?>
