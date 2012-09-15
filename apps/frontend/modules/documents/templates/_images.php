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

if ($nb_images == 0)
{
?>
    <p class="default_text"><?php echo __('No image linked to this document') ?></p>
<?php
}
else
{
    echo javascript_tag('lightbox_msgs = Array("' . __('View image details') . '","' . __('View original image') . '");');
    // param for ajax reorder
    ?>
    <div id="sortable_feedback" class="<?php echo sfConfig::get('app_ajax_feedback_div_style_inline') ?>" style="display:none;"></div>
    <p class="tips"><?php if (!$mobile_version) echo __('click thumbnails top-right corner to see image details') ?></p>
    <?php
    
    include_partial('images/linked_images', array('images' => $images,
                                                  'module_name' => $module_name,
                                                  'document_id' => $document_id,
                                                  'user_can_dissociate' => $user_can_dissociate));
}

$module_url = $module_name;
if (!$mobile_version)
{
    if (in_array($module_name, array('routes', 'sites')))
    {
        $module_url = 'o' . $module_name;
        $text = 'List all images of associated outings';
    }
    elseif (in_array($module_name, array('summits', 'parkings', 'huts')))
    {
        $module_url = 'r' . $module_name;
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
           . link_to(__($text), "images/list?$module_url=$list_ids", array('rel' => 'nofollow'))
           . ' - ' . link_to(__('collaborative images of associated outings'), "images/list?ityp=1&join=outing&$module_name=$list_ids&orderby=odate&order=desc", array('rel' => 'nofollow'));
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
            $js = 'if (!Prototype.Browser.IE || (parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) > 7)) { url = \'' .
                  url_for("@image_jsupload?mod=$module_name&document_id=$document_id").'?plupload=true' .
                  '\' } else { url = this.href; } Modalbox.show(url, {title:this.title, width:700}); return false;';
            break;
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
    // FIXME: find and delete sortable_feedback div
    echo '<!--[if IE 6]>', javascript_tag("
document.observe('dom:loaded', function(){
$$('.image_list .image').each(function(obj){
obj.down('.image_actions').hide();obj.down('.image_license').hide();
obj.observe('mouseover', function(e){obj.down('.image_actions').show();obj.down('.image_license').show();});
obj.observe('mouseout', function(e){obj.down('.image_actions').hide();obj.down('.image_license').hide();});
});});"), '<![endif]-->';
}
