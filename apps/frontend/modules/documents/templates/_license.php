<?php
use_helper('Button');

$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix');
$license_url .= $sf_user->getCulture();
$license_name = 'Creative Commons ' . __($license);
$license_title = __("$license title");
?>
<div id="license_box">
<?php
$cc_file = 'cc-' . $license . '.png';
echo '<div id="cc">' . link_to(image_tag(sfConfig::get('app_static_url') . '/static/images/' . $cc_file,
                       array('alt' => 'CC', 'title' => 'Creative Commons', 'width' => '88', 'height' => '31')),
             getMetaArticleRoute('licenses', false, 'cc-' . $license)) . '</div>';
echo ' ';
echo __('Page under %1% license',
        array('%1%' => "<a rel=\"license\" href=\"$license_url\" title=\"$license_title\">$license_name</a>"));
echo '<br />' . __('Images are under license specified in the original document of each image');
?>
</div>
