<?php
use_helper('Link', 'General', 'Url');
$mobile_version = c2cTools::mobileVersion();

echo '<div class="image_list">';
// we order them by datetime (oldest first), then by id if no datetime
// it is already order by id
$images = $images->getRawValue();
usort($images, array('c2cTools', 'cmpDateTimeDesc'));

if ($mobile_version)
{
    $swipe_i18n = array_map('__', array('Big size', 'Original image', 'Informations'));
    echo javascript_tag('var swipe_i18n = {"Big size": "'.__('Big size').'",
        "Original image": "'.__('Original image').'",
        "Informations": "'.__('Informations').'"};');
}

foreach($images as $image)
{
    $caption = $image['name'];
    $slug = make_slug($image['name']);
    $lang = $image['culture'];
    $image_id = $image['id'];
    $image_type = $image['image_type'];

    $image_tag = image_tag(image_url($image['filename'], 'small'),
                           array('alt' => $caption, 'title' => $caption));
                           
    $view_details = link_to('details', "@document_by_id_lang_slug?module=images&id=$image_id&lang=$lang&slug=$slug", 
                            array('class' => 'view_details', 'title' => __('View image details')));

    $view_original = link_to('original', absolute_link(image_url($image['filename'], null, true), true),
                             array('class' => 'view_original', 'title' => __('View original image')));

    $edit_image = link_to('edit', "@document_edit?module=images&id=$image_id&lang=$lang",
                          array('class' => 'edit_image', 'title' => __('edit_tab_help')));

    if ($user_can_dissociate)
    {
        $type = c2cTools::Module2Letter($module_name).'i';
        $strict = (int)($type == 'ii');
        $link = '@default?module=documents&action=removeAssociation&main_' . $type . '_id=' . $document_id
              . '&linked_id=' . $image_id . '&type=' . $type . '&strict=' . $strict . '&reload=1';
        $remove_association = link_to('unlink', $link,
                                      array('class' => 'unlink',
                                        'confirm' => __("Are you sure you want to unlink image %1% named \"%2%\" ?", array('%1%' => $image_id, '%2%' => $caption)),
                                        'title' => __('Unlink this association')));
    }
    else
    {
        $remove_association = '';
    }

    $view_big = link_to($image_tag,
                        ($mobile_version ? "@document_by_id_lang_slug?module=images&id=$image_id&lang=$lang&slug=$slug"
                                           : absolute_link(image_url($image['filename'], 'big', true), true)),
                        array('title' => $caption,
                              'data-lightbox' => 'document_images',
                              'class' => 'view_big',
                              'id' => 'lightbox_' . $image_id . '_' . $image_type));

    echo '<div class="image" id="image_id_' . $image_id . '">'
        . $view_big;
    if (!$mobile_version)
    {
        echo '<div class="image_actions">'
            . $view_details . $view_original . $edit_image . $remove_association
            . '</div>';
    }
    echo '<div class="image_license license_' . $image_type . '"></div></div>';
}

echo '</div>';
