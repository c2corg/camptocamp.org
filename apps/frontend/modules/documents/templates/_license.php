<?php
use_helper('Button');

if (empty($license))
{
    switch ($sf_context->getModuleName())
    {
        case 'outings':
        case 'users':
            $license = 'by-nc-nd';
            break;
    
        default:
            $license = 'by-sa';
    }
}
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix');
$license_url .= $sf_user->getCulture();
$license_name = 'Creative Commons ' . __($license);
$license_title = __("$license title");
?>
<div id="license_box">
<?php
$cc_file = ($sf_user->getCulture() == 'fr') ? 'cc_fr.gif' : 'cc_en.gif';
echo link_to(image_tag(sfConfig::get('app_static_url') . '/static/images/' . $cc_file,
                       array('id' => 'cc', 'alt' => 'CC', 'title' => 'Creative Commons')),
             getMetaArticleRoute('licenses'));
echo ' ';
echo __('Page under %1% license',
        array('%1%' => "<a href=\"$license_url\" title=\"$license_title\">$license_name</a>"));
?>
</div>
