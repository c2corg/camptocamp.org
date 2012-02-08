<?php
use_helper('Field');

$id = $sf_params->get('id');
$is_connected = $sf_user->isConnected();
$is_not_archive = !$document->isArchive();
$is_not_merged = !$document->get('redirects_to');
$mobile_version = c2cTools::mobileVersion();
$show_link_tool = ($is_not_archive && $is_not_merged && $is_connected && !$mobile_version);

if ($document->get('shelter_type') == 5)
{
    $access_label = 'access';
}
else
{
    $access_label = null;
}

echo field_text_data_if_set($document, 'staffed_period', null, array('needs_translation' => $needs_translation, 'images' => $images));
echo field_text_data_if_set($document, 'description', null, array('needs_translation' => $needs_translation, 'images' => $images));

if (count($associated_routes))
{
    echo field_text_data($document, 'pedestrian_access', $access_label, array('needs_translation' => $needs_translation, 'images' => $images));
    include_partial('routes/linked_routes', array('associated_routes' => $associated_routes,
                                                  'document' => $document,
                                                  'id' => $id,
                                                  'module' => 'huts',
                                                  'type' => '',
                                                  'strict' => true));
}
else
{
    echo field_text_data_if_set($document, 'pedestrian_access', $access_label, array('needs_translation' => $needs_translation, 'images' => $images));
}
if ($show_link_tool)
{
    echo '<div class="add_content">'
         . link_to(picto_tag('picto_add', __('Associate new access')) .
                   __('Associate new access'),
                   "@hut_addroute?document_id=$id")
         . '</div>';
}
