<?php
use_helper('Button');
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix');
$license_url .= $sf_user->getCulture();
$license_name = 'Creative Commons ' . __($license);
$license_title = __("$license title");
?>
<div id="license_box">
<?php
echo '<div id="cc">' . link_to(picto_tag('cc-'.$license),
             getMetaArticleRoute('licenses', false, 'cc-' . $license), array('title' => 'Creative Commons')) . '</div>';
echo ' ';
echo __('Page under %1% license',
        array('%1%' => "<a rel=\"license\" href=\"$license_url\" title=\"$license_title\">$license_name</a>"));
echo '<br />' . __('Images are under license specified in the original document of each image');
?>
</div>
