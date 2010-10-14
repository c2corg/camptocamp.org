<?php
use_helper('Button');
$iscopyright = $license == 'copyright';
$license_url = sfConfig::get('app_licenses_base_url') . $license . sfConfig::get('app_licenses_url_suffix');
$license_url .= $sf_user->getCulture();
$license_name = 'Creative Commons ' . __($license);
$license_title = __("$license title");
$class = 'license_box';
if (isset($large) && $large)
{
    $class .= ' large';
}
?>
<div class="<?php echo $class ?>">
<?php
echo '<div class="cc">' . link_to(picto_tag(($iscopyright ? '' : 'cc-') . $license),
             getMetaArticleRoute('licenses', false, ($iscopyright ? '' : 'cc-') . $license), array('title' => ($license != 'copyright' ? 'Creative Commons' : 'Copyright'))) . '</div>';
echo ' ';
if ($iscopyright)
{
    echo __('Image under copyright license');
}
else
{
    echo __('Page under %1% license',
        array('%1%' => "<a rel=\"license\" href=\"$license_url\" title=\"$license_title\">$license_name</a>"));
}
echo '<br />' . __('Images are under license specified in the original document of each image');
?>
</div>
