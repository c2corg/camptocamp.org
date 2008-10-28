<?php if (count($associated_docs)): ?>
<div class="one_kind_association">
<div class="association_content">
<?php
echo '<div class="assoc_img assoc_img_'.$module.'" title="'.ucfirst(__($module)).'"><span>'.ucfirst(__($module)).'&nbsp;:</span></div>';
foreach ($associated_docs as $doc)
{
    echo '<div class="linked_elt">';
    echo ' ' . link_to(
                    ucfirst($doc['name']),
                    "@document_by_id?module=$module&id=" . $doc['id']
                      );
    echo '</div>';
}
?>
</div>
</div>
<?php endif ?>
