<?php
$id = $sf_params->get('id');
$module = $sf_params->get('module');

$points = $sf_data->getRaw('points');
if (!in_array($module, array('maps', 'areas')))
{
    $points = array_map(function($v) { return explode(',', str_replace(array('(', ')'), '', $v)); }, explode('),(', $points));
}
else
{
    $geoms = array();
    $polygons = explode(')),((', $points);
    foreach($polygons as $polygon) {
        $boundaries = explode('),(', $polygon);
        $subs = array();
        foreach ($boundaries as $boundary)
        {
            $subs[] = explode(',', str_replace(array('(', ')'), '', $boundary));
        }
        $geoms[] = $subs;
    }
}

$resource_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/$id/" . $sf_params->get('lang') . "/$slug";
$popup_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/popup/$id/" . $sf_params->get('lang');
switch ($module)
{
    case 'summits':
    case 'hust':
    case 'products':
    case 'sites':
    case 'parkings':
    case 'images':
    case 'users':
        $icon = '/static/images/modules/'.$module.'.png';
        $scale = 0.6;
        break;
    default:
        $icon = '/static/images/picto/puce.png';
        $scale = 0.3;
        break;
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<kml xmlns="http://earth.google.com/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
<Document>
    <Style id="allstyle">
        <LineStyle>
            <width>5</width>
            <color>ff0099ee</color>
        </LineStyle>
        <PolyStyle>
            <color>700099ee</color>
        </PolyStyle>
        <IconStyle>
            <scale><?php echo $scale ?></scale>
            <Icon>
                <href>http://<?php echo $_SERVER['HTTP_HOST'].$icon ?></href>
            </Icon>
        </IconStyle>
    </Style>
    
    <atom:link><?php echo $resource_url ?></atom:link>
    <name><![CDATA[<?php echo $sf_data->getRaw('name') ?>]]></name>

<?php if (in_array($module, array('summits', 'sites', 'huts', 'products', 'users', 'parkings', 'images'))): ?>
    <Placemark id="<?php echo $id ?>">
        <name><![CDATA[<?php echo $sf_data->getRaw('name') ?>]]></name>
        <description><![CDATA[
            <iframe frameborder="0" src="<?php echo $popup_url ?>" width="420" height="300">
            <a href="<?php echo $resource_url ?>"><?php echo $sf_data->get('name') ?></a>
            </iframe>
        ]]></description>
        <styleUrl>#allstyle</styleUrl>
        <?php $_point = explode(' ', trim($points[0][0])); ?>
        <Point>
            <coordinates>
                <?php echo number_format($_point[0], 6, '.', '') ?>,<?php echo number_format($_point[1], 6, '.', '');
                if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; echo "\n"; ?>
            </coordinates>
        </Point>
    </Placemark>
<?php elseif (in_array($module, array('maps', 'areas'))): ?>
    <Placemark>
        <name><![CDATA[<?php echo $sf_data->getRaw('name') ?>]]></name>
        <description><![CDATA[
            <iframe frameborder="0" src="<?php echo $popup_url ?>" width="420" height="300">
            <a href="<?php echo $resource_url ?>"><?php echo $sf_data->get('name') ?></a>
            </iframe>
        ]]></description>
        <styleUrl>#allstyle</styleUrl>
        <MultiGeometry>
            <?php foreach ($geoms as $geom): ?>
            <Polygon>
                <?php foreach ($geom as $key => $points):
                echo (($key == 0) ? '<outerBoundaryIs>' : '<innerBoundaryIs>') ?>
                    <LinearRing>
                        <tessellate>1</tessellate>
                        <altitudeMode>clampToGround</altitudeMode>
                        <coordinates>
                        <?php foreach ($points as $point):
                            $_point = explode(' ', trim($point));
                            echo number_format($_point[0], 6, '.', '') ?>,<?php echo number_format($_point[1], 6, '.', '');
                            if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; echo "\n";
                        endforeach; ?>
                        </coordinates>
                    </LinearRing>
                <?php echo (($key == 0) ? '</outerBoundaryIs>' : '</innerBoundaryIs>');
                endforeach; ?>
            </Polygon>
            <?php endforeach; ?>
        </MultiGeometry>
    </Placemark>
<?php elseif (in_array($module, array('routes', 'outings'))): ?>
    <Folder>
        <name><![CDATA[<?php echo $sf_data->getRaw('name') ?>]]></name>
        <Placemark>
            <name>Path</name>
            <styleUrl>#allstyle</styleUrl>
            <description><![CDATA[
                <iframe frameborder="0" src="<?php echo $popup_url ?>" width="420" height="300">
                <a href="<?php echo $resource_url ?>"><?php echo $sf_data->get('name') ?></a>
                </iframe>
            ]]></description>
            <MultiGeometry>
                <?php foreach ($points as $line): ?>
                <LineString>
                    <coordinates>
                        <?php foreach ($line as $point):
                            $_point = explode(' ', trim($point));
                            echo number_format($_point[0], 6, '.', '') ?>,<?php echo number_format($_point[1], 6, '.', '');
                            if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; echo "\n";
                        endforeach; ?>
                    </coordinates>
                </LineString>
                <?php endforeach ?>
            </MultiGeometry>
        </Placemark>
        <Folder>
            <name>Points</name>
            <styleUrl>#allstyle</styleUrl>
        <?php $i = 0;
        foreach ($points as $line):
            foreach ($line as $point):
            $_point = explode(' ', trim($point));
            $lon = number_format($_point[0], 6, '.', '');
            $lat = number_format($_point[1], 6, '.', ''); ?>
            <Placemark>
                <name><?php echo $i ?></name>
                <styleUrl>#allstyle</styleUrl>
                <ExtendedData><?php // TODO check ?>
                    <?php if (count($_point) > 2 && abs($_point[2])>1): ?>
                    <Data name="Altitude">
                        <value><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); ?>  m</value>
                    </Data>
                    <?php endif;
                    if (count($_point) > 3 && ($_point[3]!=0)): ?>
                    <Data name="Time">
                        <value><?php echo date('G:i:s', $_point[3]) ?></value>
                    </Data>
                    <?php endif; ?>
                </ExtendedData>
                <LookAt>
                    <longitude><?php echo $lon ?></longitude>
                    <latitude><?php echo $lat ?></latitude>
                    <?php if (count($_point) > 2 && abs($_point[2])>1): ?>
                    <elevation><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); ?></elevation>
                    <?php endif; echo "\n"; ?>
                    <tilt>66</tilt>
                </LookAt>
                <?php if (count($_point) > 3): ?>
                <TimeStamp><when><?php echo date('c', $_point[3]); ?></when></TimeStamp>
                <?php endif; echo "\n"; ?>
                <Point>
                        <coordinates><?php echo $lon ?>,<?php echo $lat; if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; ?></coordinates>
                </Point>
            </Placemark>
        <?php  $i++; endforeach; endforeach; ?>
        </Folder>
    </Folder>
<?php endif ?>
  </Document>
</kml>
