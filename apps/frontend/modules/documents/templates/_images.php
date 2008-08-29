<?php
use_helper('ModalBox', 'Link', 'Lightbox', 'Javascript', 'MyImage');
// add lightbox ressources
_addLbRessources(false);

$module_name = $sf_context->getModuleName();
$nb_images = count($images);

// Why is this useful ?
$sf_user->setAttribute('module', $module_name);

echo start_section_tag('Images', 'images');

// rights check (everyone connected, owner+moderator only, moderator only)
if ($special_rights == 'user')
{
    $specifics_rights = true;
}
else if ($special_rights == 'moderator')
{
    $specifics_rights = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
}
else if ($special_rights == 'owner')
{
    $specifics_rights = ($sf_user->hasCredential(sfConfig::get('app_credentials_moderator')) || $sf_user->isDocumentOwner($document_id));
}
$user_valid = $sf_user->isConnected() && $specifics_rights;

if ($nb_images == 0): ?>
    <p><?php echo __('No image linked to this document') ?></p>
<?php else:
    // param for ajax reorder
    ?>
    <div id="sortable_feedback" class="<?php echo sfConfig::get('app_ajax_feedback_div_style_inline') ?>" style="display:none;"></div>
    <div id="image_list">
    <?php
    foreach($images as $image):
        
        $caption = $image['name'];
        $image_id = $image['id'];

        $image_tag = image_tag(image_url($image['filename'], 'small'),
                               array('alt' => $caption));
                               
        $view_details = link_to('details', "@document_by_id?module=images&id=$image_id", 
                                array('class' => 'view_details', 'title' => __('View image details')));
        
        $remove_association = ($user_valid) ? 
                                    link_to('unlink', "@image_unlink?image_id=$image_id&document_id=$document_id", 
                                              array('class' => 'unlink', 
                                                    'confirm' => __("Are you sure you want to unlink image %1% named \"%2%\" ?", array('%1%' => $image_id, '%2%' => $caption)), 
                                                    'title' => __('Unlink this association')))
                                    : '' ;
                                
        $view_big = link_to($image_tag, absolute_link(image_url($image['filename'], 'big')), array(
                                                                    'title' => $caption,
                                                                    'rel' => 'lightbox[document_images]',
                                                                    'class' => 'view_big'));
    ?>
        <div class="image" id="image_id_<?php echo $image_id ?>">
            <?php echo $view_big ?>
            <div class="image_actions">
                <?php echo $view_details . $remove_association ?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <p class="tips"><?php echo __('click thumbnails top-right corner to see image details') ?></p>
<?php endif;

if($user_valid)
{
    $add = __('add an image');
    echo '<p style="clear:left">';
    echo m_link_to(image_tag("/static/images/picto/plus.png",
                             array('title' => $add,
                                   'alt' => $add)
                            ) . $add,
                   "@image_upload?mod=$module_name&document_id=$document_id",
                   array('title' => $add,
                         'class' => 'add_content'));
    echo '</p>';
}

echo end_section_tag();

if ($nb_images > 0)
{
    // FIXME: find and delete sortable_feedback div
    echo javascript_tag("
         Event.observe(window, 'load', function(){
             $$('.image_actions').invoke('hide');
             
             $$('.image').each(function(obj){
    
                 obj.observe('mouseover', function(e){
                      obj.down('.image_actions').show();
                  });
                  
                  obj.observe('mouseout', function(e){
                       obj.down('.image_actions').hide();
                   });
             });
         });");
    // FIXME: do a separate JS file for that, and dynamically include it in the response.
}
