<?php


if ($hide_image_type_edit)
{
    echo input_hidden_tag('image_type', $document->get('image_type'));
}
else
{
    if ($allow_copyright)
    {
        echo object_group_dropdown_tag($document, 'image_type', 'mod_images_type_full_list');
    }
    else
    {
        echo object_group_dropdown_tag($document, 'image_type', 'mod_images_type_list');
    }
}
?>