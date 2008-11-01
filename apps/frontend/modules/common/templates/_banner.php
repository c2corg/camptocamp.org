<?php if (isset($banner['type']) && $banner['type'] == 'flash'): ?>
    <?php
    $width = $banner['width'];
    $height = $banner['height'];
    $file = sfConfig::get('app_static_url') . '/static/images/pub/' . $banner['file'];
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

<?php elseif (isset($banner['type']) && $banner['type'] == 'adsense'): ?>
<script type="text/javascript"><!--
google_ad_client = "pub-8662990478599655";
google_ad_slot = "5346820278";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<?php else: ?>
    <a href="<?php echo $counter_base_url . $banner['id'] ?>"><?php
    echo image_tag(sfConfig::get('app_static_url') . '/static/images/pub/' . $banner['image'],
                   array('id' => 'banner', 'alt' => $banner['alt'], 'title' => $banner['alt'])) ;
    ?></a>
<?php endif ?>
