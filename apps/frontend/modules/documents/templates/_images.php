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
else
{
    use_javascript('/static/js/swipe.js', 'last');
    use_javascript('/static/js/swipe.wrapper.js', 'last');
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
    ?>
    <p class="tips"><?php if (!$mobile_version) echo __('click thumbnails top-right corner to see image details') ?></p>
    <?php
    
    include_partial('images/linked_images', array('images' => $images,
                                                  'module_name' => $module_name,
                                                  'document_id' => $document_id,
                                                  'user_can_dissociate' => $user_can_dissociate));
}

if (!$mobile_version)
{
    $images_link = $outings_link = $link = array();
    $module_url = $module_url2 = $module_name;
    $act_url = '';
    $orderby = '';
    if (!isset($list_ids))
    {
        $list_ids = $document_id;
    }
    
    if (in_array($module_name, array('routes', 'sites')))
    {
        $module_url = 'o' . $module_name;
        if ($module_name == 'sites')
        {
            $act_url = '&act=4';
        }
        $orderby = '&orderby=odate&order=desc';
        $text = 'List all images of associated outings';
    }
    elseif (in_array($module_name, array('summits', 'parkings', 'huts')))
    {
        $module_url = 'r' . $module_name;
        $text = 'List all images of associated routes';
    }
    elseif ($nb_images)
    {
        if ($module_name == 'articles')
        {
            $module_url = 'itags';
        }
        else
        {
            $module_url = 'docs';
        }
        $text = 'List all linked images';
        if (in_array($module_name, array('outings', 'articles')))
        {
            $orderby = '&orderby=date&order=asc';
        }
    }
    
    if (isset($text))
    {
        $images_link[] = link_to(__($text), "images/list?$module_url=$list_ids$act_url$orderby", array('rel' => 'nofollow'));
        
        if ($nb_images && in_array($module_name, array('summits', 'routes', 'sites', 'articles')))
        {
            $outings_link[] = link_to(__('Outings linked to these images'), "outings/list?itags=$list_ids", array('rel' => 'nofollow'));
        }
    }
    
    $url2 = '';
    $text2 = 'collaborative images of associated outings';
    if ($module_name == 'outings')
    {
        if ($nb_images)
        {
            $url2 = "images/list?ityp=1&docs=$list_ids&orderby=date&order=asc";
            $text2 = 'collaborative images';
        }
    }
    elseif ($module_name == 'articles')
    {
        $url2 = "images/list?ityp=1&dtags=$list_ids&orderby=date&order=asc";
        $text2 = 'collaborative images of associated documents';
    }
    else
    {
        $url2 = "images/list?ityp=1&join=outing&$module_name=$list_ids$act_url&orderby=odate&order=desc";
    }
    
    if (!empty($url2) && !in_array($module_name, array('images', 'users')))
    {
        $images_link[] = link_to(__($text2), $url2, array('rel' => 'nofollow'));
    }
    
    if (count($images_link))
    {
        $link[] = picto_tag('picto_images') . ' ' . implode(' - ', $images_link);
    }
    if (count($outings_link))
    {
        $link[] = picto_tag('picto_outings') . ' ' . implode(' - ', $outings_link);
    }
    
    if (count($link))
    {
        echo '<p class="list_link">'
           . implode(' - ', $link)
           . '</p>';
    }
}

if ($connected && ($module_name != 'images') && (!$is_protected || $moderator)): ?>
    <div id="add_images_button" class="add_content"<?php if ($mobile_version) echo 'style="display:none" ' ?>>
    <?php
    $response = sfContext::getInstance()->getResponse();

    $add = __('add an image');

    if ($mobile_version)
    {
        echo link_to(picto_tag('picto_add', $add) . $add,
                     "@image_jsupload?mod=$module_name&document_id=$document_id",
                     array('title' => $add));
    }
    else
    {
        $upload_method = sfConfig::get('app_images_upload_method', 'js');
        $js = "var width = $(window).width() - 60;";
        switch ($upload_method)
        {
            // note: ie<=7 doesn't support jsupload nor plupload
            case 'js':
                $js .= 'if (!/MSIE [67].0/.exec(navigator.userAgent)) { url = \'' .
                      url_for("@image_jsupload?mod=$module_name&document_id=$document_id") . '?noplupload=true' .
                      '\' } else { url = this.href; } $.modalbox.show({remote:url,title:this.title,width:width}); return false;';
                break;
            case 'plupload':
                $js .= 'if (!/MSIE [67].0/.exec(navigator.userAgent)) { url = \'' .
                      url_for("@image_jsupload?mod=$module_name&document_id=$document_id") .
                      '\' } else { url = this.href; } $.modalbox.show({remote:url,title:this.title,width:width}); return false;';
                break;
            default:
                $js = '$.modalbox.show({remote:\''.url_for("@image_upload?mod=$module_name&document_id=$document_id") .
                      '\',title:this.title,width:700}); return false;';
                break;
        }

        echo link_to(picto_tag('picto_add', $add) . $add,
                     "@image_upload?mod=$module_name&document_id=$document_id",
                     array('onclick' => $js, 'title' => $add));
    }


    // for mobile version we test if file input is enabled, as well
    // as some xhr support
    // this should be enough in order to have plupload run in html5 mode
    // file input test is taken from modernizr
    if ($mobile_version)
    {
        echo javascript_tag("if ((function() {
          var xhr;
          if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
            if (!xhr.sendAsBinary && !xhr.upload) return false;
          } else {
            return false;
          }
          if (navigator.userAgent.match(/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) {
            return false;
          }
          var e = document.createElement('input');
          e.type = 'file';
          return !e.disabled;
        })()) { document.getElementById('add_images_button').style.display = ''; }");
    }

    // if user is not the author, and only the author can add images,
    // hide the button for uploading images to the document.
    // Moreover, if they are no images linked, simply hide the section
    if (isset($author_specific) && $author_specific)
    {
        if ($nb_images)
        {
            echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) document.getElementById('add_images_button').style.display = 'none';");
        }
        else
        {
            echo javascript_tag("if (!document.body.hasAttribute('data-user-author')) {
              document.getElementById('images_tbg').style.display = 'none';
              document.getElementById('images_section_container').style.display = 'none';
              document.getElementById('images_anchor').style.display = 'none';
            }");
        }
    }
    ?>
    </div>
<?php endif;

echo end_section_tag();
