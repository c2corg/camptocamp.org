<?php 
$points = $sf_data->getRaw('points');
$nbpts = count($points);
$id = $sf_params->get('id');
$resource_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $sf_context->getModuleName() . "/$id/" . $sf_params->get('lang') . "/$slug"; 
?>
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
<Document>
    <Style id="allstyle">
        <LineStyle>
            <width>5</width>
            <color>ff0099ee</color>
        </LineStyle>
        <PolyStyle>
            <color>900099ee</color>
        </PolyStyle>
        <IconStyle>
            <scale>0.3</scale>
            <Icon>
                <href>http://<?php echo $_SERVER['HTTP_HOST'] ?>/static/images/picto/puce.png</href>
            </Icon>
        </IconStyle>
    </Style>
    
    <ExtendedData>
        <Data name="module">
            <value><?php echo $sf_context->getModuleName() ?></value>
        </Data>
        <Data name="id">
            <value><?php echo $id ?></value>
        </Data>
        <Data name="culture">
            <value><?php echo $sf_params->get('lang') ?></value>
        </Data>
        <Data name="name">
            <value><?php echo $sf_data->getRaw('name') ?></value>
        </Data>
    </ExtendedData>    

    <atom:link><?php echo $resource_url ?></atom:link>
    <name><?php echo $sf_data->getRaw('name') ?></name>

<?php if ($nbpts > 1): ?>
    <Folder>
        <name>Tracks</name>
        <Folder>
            <name><?php echo $sf_data->getRaw('name') ?></name>
            <Folder>
                <name>Points</name>
<?php $i = 0; 
                foreach ($points as $point):
                $_point = explode(' ', trim($point)); 
                $lon = number_format($_point[0], 6, '.', '');
                $lat = number_format($_point[1], 6, '.', ''); ?>
                <Placemark>
                    <name><?php echo $i ?></name>
                    <description><![CDATA[
                        <table>
                            <?php if (count($_point) > 2 && abs($_point[2])>1): ?><tr><td><b>Altitude:</b> <?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); ?>  m </td></tr><?php endif; ?> 
                            <?php if (count($_point) > 3 && ($_point[3]!=0)): ?><tr><td><b>Time:</b> <?php echo date('G:i:s', $_point[3]) ?> </td></tr><?php endif; ?>
                        </table>
                 ]]></description>
                    <LookAt>
                        <longitude><?php echo $lon ?></longitude>
                        <latitude><?php echo $lat ?></latitude>
<?php if (count($_point) > 2 && abs($_point[2])>1): ?>
                        <elevation><?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); ?></elevation><?php endif; echo "\n"; ?>
                        <tilt>66</tilt>
                    </LookAt>
<?php if (count($_point) > 3): ?>
                    <TimeStamp><when><?php echo date('c', $_point[3]); ?></when></TimeStamp><?php endif; echo "\n"; ?>
                    <styleUrl>#allstyle</styleUrl>
                    <Point>
                        <coordinates><?php echo $lon ?>,<?php echo $lat; if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; ?></coordinates>
                    </Point>
                </Placemark>
<?php $i++; endforeach ?>
            </Folder>
            <Placemark>
                <name>Path</name>
                <description><![CDATA[<?php echo $sf_data->getRaw('description') ?><br /><a href="<?php echo $resource_url ?>"><?php echo $resource_url ?></a>]]></description>
                <styleUrl>#allstyle</styleUrl>
                <LineString>
                    <coordinates>
<?php foreach ($points as $point):
                        $_point = explode(' ', trim($point)); ?>
                        <?php echo number_format($_point[0], 6, '.', '') ?>,<?php echo number_format($_point[1], 6, '.', ''); 
                        if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; echo "\n"; 
endforeach ?>
                    </coordinates>
                </LineString>
            </Placemark>
        </Folder>
    </Folder>
<?php elseif ($nbpts = 1): ?>
    <Placemark id="<?php echo $id ?>">
        <name><?php echo $sf_data->getRaw('name') ?></name>
        <description><![CDATA[<?php echo $sf_data->getRaw('description') ?><br /><a href="<?php echo $resource_url ?>"><?php echo $resource_url ?></a>]]></description>
        <styleUrl>#allstyle</styleUrl>
<?php $_point = explode(' ', trim($points[0])); ?>
        <Point>
            <coordinates>
                <?php echo number_format($_point[0], 6, '.', '') ?>,<?php echo number_format($_point[1], 6, '.', '');
                if (count($_point) > 2): ?>,<?php echo (abs($_point[2])<1) ? '0' : round($_point[2]); endif; echo "\n"; ?>
            </coordinates>
        </Point>
    </Placemark>
<?php endif ?>
  </Document>
</kml>



