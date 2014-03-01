<?php 
$culture = __('meta_language');

if (isset($banner['type']) && $banner['type'] == 'flash'): //// CUSTOM FLASH BANNER ////
    $width = $banner['width'];
    $height = $banner['height'];
    $file = sfConfig::get('app_static_url') . '/static/images/pub/' . (isset($banner['file_'.$culture]) ? $banner['file_'.$culture] : $banner['file']);
    if (isset($banner['id']))
    {
        $file .= '?clickTAG=' . $sf_request->getUriPrefix() . sfConfig::get('mod_common_counter_base_url') . $banner['id'];
    }
    ?>
    <object type="application/x-shockwave-flash" data="<?php echo $file ?>"
    width="<?php echo $width ?>" height="<?php echo $height ?>" id="banner">
        <param name="movie" value="<?php echo $file ?>" />
        <param name="quality" value="high" />
    </object>


<?php elseif (isset($banner['type']) && $banner['type'] == 'adsense'): //// GOOGLE ADSENSE //// ?>
<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<ins class="adsbygoogle" style="display:inline-block;width:468px;height:60px" data-ad-client="ca-pub-8662990478599655" data-ad-slot="5346820278"></ins>
<script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>


<?php elseif (isset($banner['type']) && $banner['type'] == 'netaffiliation'): //// NETAFFILIATION //// ?>
<!--[if !IE]><!-->
<object data="http://action.metaffiliation.com/emplacement.php?emp=45475Ie8475b6313ea8b6e" type="text/html" width="468" height="60"></object>
<!--<![endif]-->
<!--[if IE]>
<iframe src="http://action.metaffiliation.com/emplacement.php?emp=45475Ie8475b6313ea8b6e" width="468" height="60" scrolling="no" frameborder="0"></iframe>
<![endif]-->

<?php elseif (isset($banner['type']) && $banner['type'] == 'adserver'): // ADSERVER ?>
<div class="ads_323739345f343337315f3135383935"><script type="text/javascript">
var rdads=new String(Math.random()).substring (2, 11);
document.write('<sc'+'ript type="text/javascript" src="http://server1.affiz.net/tracking/ads_display.php?n=323739345f343337315f3135383935_3418e88a4d&rdads='+rdads+'"></sc'+'ript>');
</script></div>

<?php elseif (isset($banner['type']) && $banner['type'] == 'adserver2'): // ADSERVER ?>
<div class="ads_323739345f343337315f3135383935">
<?php
$rdads = '';
for ($i = 0; $i < 9; $i++) {
    $rdads .= rand(0, 9);
}
echo javascript_queue("$.getScript('http://server1.affiz.net/tracking/ads_display.php?n=323739345f343337315f3135383935_3418e88a4d&rdads=" . $rdads . "');");
?>
</div>

<?php else: //// CUSTOM IMAGE BANNER ////
    $id = isset($banner['id_'.$culture]) ? $banner['id_'.$culture] : $banner['id']; ?>
    <a href="<?php echo $counter_base_url . $id ?>"><?php
    $image = isset($banner['image_'.$culture]) ? $banner['image_'.$culture] : $banner['image'];
    $size = @getimagesize(sfConfig::get('sf_web_dir') . '/static/images/pub/' . $image);
    echo image_tag(sfConfig::get('app_static_url') . '/static/images/pub/' . $image,
                   array('id' => 'banner', 'alt' => $banner['alt'], 'title' => $banner['alt'],
                         'width' => $size[0], 'height' => $size[1]));
    ?></a>
<?php endif ?>
