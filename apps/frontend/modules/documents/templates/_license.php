<?php
use_helper('Button', 'Date');

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
<footer class="<?php echo $class ?>">
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

if (isset($version) && !c2cTools::mobileVersion())
{
    echo '<br /><span class="doc_infos">',
    __('Version #%1%, date %2%', array('%1%' => $version, '%2%' => format_date($created_at, 'D')));

    if ($sf_user->hasCredential(sfConfig::get('app_credentials_moderator')))
    {
        echo '<span class="no_print"> - ',
             __('Document generated %1% in %2%', array('%1%' => format_datetime(time()),
                                                       '%2%' => round(1000 * $timer->getElapsedTime()))),
             '</span>';
    }
    echo '</span>';
}
?>
</footer>
