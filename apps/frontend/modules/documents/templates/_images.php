<?php
use_helper('ModalBox', 'Link', 'Lightbox', 'Javascript', 'MyImage', 'General');
// add lightbox ressources
addLbMinimalRessources();

$module_name = $sf_context->getModuleName();
$nb_images = count($images);

// Why is this useful ?
$sf_user->setAttribute('module', $module_name);

echo start_section_tag('Images', 'images');

$connected = $sf_user->isConnected();
$moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));

if ($dissociation == 'user')
{
    $specifics_rights = true;
}
else if ($dissociation == 'moderator')
{
    $specifics_rights = $moderator;
}
// TODO due to cache it is impossible to have owner as special_rights
// For the moment only moderators can dissociate images
$user_can_dissociate = $sf_user->isConnected() && $specifics_rights;

if ($nb_images == 0): ?>
    <p><?php echo __('No image linked to this document') ?></p>
<?php else:
    // param for ajax reorder
    ?>
    <div id="sortable_feedback" class="<?php echo sfConfig::get('app_ajax_feedback_div_style_inline') ?>" style="display:none;"></div>
    <p class="tips"><?php echo __('click thumbnails top-right corner to see image details') ?></p>
    <div id="image_list">
    <?php
    foreach($images as $image):
        
        $caption = $image['name'];
        $slug = formate_slug($image['search_name']);
        $lang = $image['culture'];
        $image_id = $image['id'];
        $image_type = $image['image_type'];

        $image_tag = image_tag(image_url($image['filename'], 'small'),
                               array('alt' => $caption));
                               
        $view_details = link_to('details', "@document_by_id_lang_slug?module=images&id=$image_id&lang=$lang&slug=$slug", 
                                array('class' => 'view_details', 'title' => __('View image details')));

        $view_original = link_to('original', absolute_link(image_url($image['filename'], null, true), true),
                                 array('class' => 'view_original', 'title' => __('View original image')));
        
        $remove_association = $user_can_dissociate ?
                              link_to('unlink', "@image_unlink?image_id=$image_id&document_id=$document_id", 
                                      array('class' => 'unlink', 
                                            'confirm' => __("Are you sure you want to unlink image %1% named \"%2%\" ?", array('%1%' => $image_id, '%2%' => $caption)), 
                                            'title' => __('Unlink this association')))
                              : '' ;
                                
        $view_big = link_to($image_tag, absolute_link(image_url($image['filename'], 'big', true), true),
                            array('title' => $caption,
                                  'rel' => 'lightbox[document_images]',
                                  'class' => 'view_big'));
    ?>
        <div class="image" id="image_id_<?php echo $image_id ?>">
            <?php echo $view_big ?>
            <div class="image_actions" style="display:none">
                <?php echo $view_details . $view_original . $remove_association ?>
            </div>
            <div class="image_license <?php echo 'license_'.$image_type ?>" style="display:none"></div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif;

if (in_array($module_name, array('summits', 'parkings', 'huts', 'routes', 'sites')))
{
    if (in_array($module_name, array('routes', 'sites')))
    {
        $associated_doc_type = 'outings';
    }
    else
    {
        $associated_doc_type = 'routes';
    }
    $module_short = substr($module_name, 0, -1);
    echo '<p style="margin-top:0.7em;">' .
        picto_tag('picto_images') . ' ' .
        link_to(__('List all images of associated ' . $associated_doc_type), "images/list?$module_short=$document_id") .
        '</p>';
}

if ($connected && ($module_name != 'images')): ?>
    <p style="clear:left" id="add_images_button">
    <?php
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('app_static_url') . '/static/js/image_upload.js?' . sfSVN::getHeadRevision('image_upload.js'), 'last');
    $add = __('add an image');
    echo m_link_to(picto_tag('picto_add', $add) . $add,
                   "@image_upload?mod=$module_name&document_id=$document_id",
                   array('title' => $add, 'class' => 'add_content'),
                   array('width' => 700),
                   array('alternate_link' => "@image_jsupload?mod=$module_name&document_id=$document_id"));
    if (isset($author_specific) && $author_specific)
    {
        echo javascript_tag("if (!user_is_author) $('add_images_button').hide();");
    }
    ?>
    </p>
<?php endif;

echo end_section_tag();

if ($nb_images > 0)
{
// FIXME: find and delete sortable_feedback div + don't use javascript for non-ie browsers
echo javascript_tag("
Event.observe(window, 'load', function(){
$$('.image').each(function(obj){
obj.observe('mouseover', function(e){obj.down('.image_actions').show();obj.down('.image_license').show();});
obj.observe('mouseout', function(e){obj.down('.image_actions').hide();obj.down('.image_license').hide();});
});});");
// FIXME: do a separate JS file for that, and dynamically include it in the response.
}
