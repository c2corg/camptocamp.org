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
    if (isset($doc['parent_id']))
    {
        $class .= ' extra';
    }
    echo '<div class="' . $class . '">';
    echo ' ' . link_to(
                    ucfirst($doc['name']),
                    "@document_by_id_lang_slug?module=$module&id=" . $doc['id'] . '&lang=' . $doc['culture'] . '&slug=' . formate_slug($doc['search_name'])
                      );
    if (is_scalar($doc['elevation']))
    {
        echo '&nbsp; ' . $doc['elevation'] . __('meters');
    }
    echo '</div>';
}
?>
</div>
</div>
<?php endif ?>
