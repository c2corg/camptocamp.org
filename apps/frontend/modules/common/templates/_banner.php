<?php if (isset($banner['type']) && $banner['type'] == 'flash'): ?>
    <?php
    $width = $banner['width'];
    $height = $banner['height'];
    $file = '/static/images/pub/' . $banner['file'];
    ?>
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
    codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"
    width="<?php echo $width ?>" height="<?php echo $height ?>" id="banner">
        <param name="movie" value="<?php echo $file ?>" />
        <param name="quality" value="high" />
        <embed src="<?php echo $file ?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer"
        type="application/x-shockwave-flash" width="<?php echo $width ?>" height="<?php echo $height ?>"></embed>
    </object>
<?php elseif (isset($banner['type']) && $banner['type'] == 'adsense'): ?>
<script type="text/javascript"><!--
google_ad_client = "pub-8662990478599655";
google_ad_slot = "8587569393";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<?php else: ?>
    <a href="<?php echo $counter_base_url . $banner['id'] ?>"><?php
    echo image_tag('/static/images/pub/' . $banner['image'],
               array('id' => 'banner', 'alt' => $banner['alt'], 'title' => $banner['alt'])) ;
    ?></a>
<?php endif ?>
