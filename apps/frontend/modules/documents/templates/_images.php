<?php
use_helper('ModalBox', 'Link', 'Lightbox', 'Javascript', 'MyImage', 'General', 'Url');

$module_name = $sf_context->getModuleName();
$nb_images = count($images);
$mobile_version = c2cTools::mobileVersion();
$connected = $sf_user->isConnected();
$moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));

if (!$mobile_version)
{
    // add lightbox ressources
    addLbMinimalRessources();
}

// FIXME Why is this useful ?
$sf_user->setAttribute('module', $module_name);

echo start_section_tag('Images', 'images');

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
    <p class="default_text"><?php echo __('No image linked to this document') ?></p>
<?php else:
    echo javascript_tag('lightbox_msgs = Array("' . __('View image details') . '","' . __('View original image') . '");');
    // param for ajax reorder
    ?>
    <div id="sortable_feedback" class="<?php echo sfConfig::get('app_ajax_feedback_div_style_inline') ?>" style="display:none;"></div>
    <p class="tips"><?php if (!$mobile_version) echo __('click thumbnails top-right corner to see image details') ?></p>
    <div class="image_list">
    <?php
    // we order them by datetime (oldest first), then by id if no datetime
    // it is already order by id
    $images = $images->getRawValue();
    usort($images, array('c2cTools', 'cmpDateTimeDesc'));

    foreach($images as $image):
        
        $caption = $image['name'];
        $slug = make_slug($image['name']);
        $lang = $image['culture'];
        $image_id = $image['id'];
        $image_type = $image['image_type'];

        $image_tag = image_tag(image_url($image['filename'], 'small'),
                               array('alt' => $caption));
                               
        $view_details = link_to('details', "@document_by_id_lang_slug?module=images&id=$image_id&lang=$lang&slug=$slug", 
                                array('class' => 'view_details', 'title' => __('View image details')));

        $view_original = link_to('original', absolute_link(image_url($image['filename'], null, true), true),
                                 array('class' => 'view_original', 'title' => __('View original image')));

        if ($user_can_dissociate)
        {
            $type = c2cTools::Model2Letter(c2cTools::module2model($module_name)).'i';
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
                                  'rel' => 'lightbox[document_images]',
                                  'class' => 'view_big',
                                  'id' => 'lightbox_' . $image_id . '_' . $image_type));
    ?>
        <div class="image" id="image_id_<?php echo $image_id ?>">
            <?php echo $view_big;
            if (!$mobile_version): ?>
            <div class="image_actions" style="display:none">
                <?php echo $view_details . $view_original . $remove_association ?>
            </div>
            <?php endif ?>
            <div class="image_license <?php echo 'license_'.$image_type ?>" <?php echo $mobile_version ? '' : 'style="display:none"' ?>></div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif;

$module_url = $module_name;
if (!$mobile_version)
{
    if (in_array($module_name, array('routes', 'sites')))
    {
        $text = 'List all images of associated outings';
    }
    elseif (in_array($module_name, array('summits', 'parkings', 'huts')))
    {
        $text = 'List all images of associated routes';
    }
    elseif ($nb_images)
    {
        $module_url = 'itags';
        $text = 'List all linked images';
    }

    if (isset($text))
    {
        if (!isset($list_ids))
        {
            $list_ids = $document_id;
        }
        echo '<p class="list_link">'
           . picto_tag('picto_images') . ' '
           . link_to(__($text), "images/list?$module_url=$list_ids", array('rel' => 'nofollow'));
        if ($nb_images && in_array($module_name, array('summits', 'routes', 'sites', 'articles')))
        {
            echo ' - ' . picto_tag('picto_outings') . ' '
               . link_to(__('Outings linked to these images'), "outings/list?itags=$list_ids", array('rel' => 'nofollow'));
        }
        echo '</p>';
    }
}

if ($connected && !$mobile_version && ($module_name != 'images') && (!$is_protected || $moderator)): ?>
    <div id="add_images_button" class="add_content">
    <?php
    $response = sfContext::getInstance()->getResponse();

    $add = __('add an image');

    $upload_method = sfConfig::get('app_images_upload_method', 'js');
    switch ($upload_method)
    {
        case 'js':
            $response->addJavascript('/static/js/image_upload.js', 'last');
            $js = 'if (!Prototype.Browser.IE || (parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) > 7)) { url = \'' .
                  url_for("@image_jsupload?mod=$module_name&document_id=$document_id") .
                  '\' } else { url = this.href; } Modalbox.show(url, {title:this.title, width:700}); return false;';
            break;
        case 'plupload':
            $response->addJavascript('/static/js/plupload.c2c.js', 'last');
            $response->addJavascript('/static/js/plupload.wrapper.js', 'last');
            $js = 'Modalbox.show(\''.url_for("@image_jsupload?mod=$module_name&document_id=$document_id").'?plupload=true\', {title:this.title, width:700}); return false;';
            break;
        default:
            $js = 'Modalbox.show(\''.url_for("@image_upload?mod=$module_name&document_id=$document_id").'\', {title:this.title, width:700}); return false;';
            break;
    }
    echo link_to(picto_tag('picto_add', $add) . $add,
                 "@image_upload?mod=$module_name&document_id=$document_id",
                 array('onclick' => $js, 'title' => $add));
    if (isset($author_specific) && $author_specific)
    {
        echo javascript_tag("if (!user_is_author) $('add_images_button').hide();");
    }
    ?>
    </div>
<?php endif;

echo end_section_tag();

if ($nb_images > 0 && !$mobile_version)
{
// FIXME: find and delete sortable_feedback div + don't use javascript for non-ie browsers
echo javascript_tag("
Event.observe(window, 'load', function(){
$$('.image_list .image').each(function(obj){
obj.observe('mouseover', function(e){obj.down('.image_actions').show();obj.down('.image_license').show();});
obj.observe('mouseout', function(e){obj.down('.image_actions').hide();obj.down('.image_license').hide();});
});});");
// FIXME: do a separate JS file for that, and dynamically include it in the response.
}
