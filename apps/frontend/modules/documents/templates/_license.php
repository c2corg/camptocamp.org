<?php
if (empty($license))
{
    switch ($sf_context->getModuleName())
    {
        case 'outings':
        case 'users':
            $license = 'by-nc-nd';
            break;
    
        default:
            $license = 'by-nc-sa';
    }
}
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix');
$license_url .= $sf_user->getCulture();
$license_name = 'Creative Commons ' . __($license);
$license_title = __("$license title");
?>
<div id="license_box">
<?php
echo image_tag(sfConfig::get('app_static_url') . '/static/images/cc.png',
               array('id' => 'cc_mini', 'alt' => 'CC', 'title' => 'Creative Commons'));
echo __('Page under %1% license',
        array('%1%' => "<a href=\"$license_url\" title=\"$license_title\">$license_name</a>"));
?>
</div>
