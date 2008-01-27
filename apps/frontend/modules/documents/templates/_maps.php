<!-- map -->
<?php 
$document = isset($document) ? $document : null;

if ($document && !$document->get('geom_wkt')): ?>
    <div id="<?php echo $container_div ?>"><div class="article_contenu">
    <?php echo __('No geom info, please edit this document to add some');?>
    </div></div>
    <?php 
else:
use_helper('Map'); 

$layers = array(
    'google_hyb'    =>
        array(
            'name'      => __('hybrid'),
            'google'    => true,
            'type'      => 'G_HYBRID_MAP',
        ),
    'google_map'    =>
        array(
            'name'      => __('map'),
            'google'    => true,
            'type'      => 'G_NORMAL_MAP',
        ),
    'google_sat'    =>
        array(
            'name'      => __('satellite'),
            'google'    => true,
            'type'      => 'G_SATELLITE_MAP',
        ),
    'google_phy'    =>
        array(
            'name'      => __('relief'),
            'google'    => true,
            'type'      => 'G_PHYSICAL_MAP',
        ),
    'summits'       =>
        array(
            'name'      => __('summits'),
            'tiled'     => false,
            'visible'   => true
        ),
    'huts'       =>
        array(
            'name'      => __('huts'),
            'tiled'     => false,
            'visible'   => false
        ),
    'parkings'       =>
        array(
            'name'      => __('parkings'),
            'tiled'     => false,
            'visible'   => false
        ),
    'sites'       =>
        array(
            'name'      => __('sites'),
            'tiled'     => false,
            'visible'   => false
        ),
    'users'       =>
        array(
            'name'      => __('users'),
            'tiled'     => false,
            'visible'   => false
        ),
    'images'       =>
        array(
            'name'      => __('images'),
            'tiled'     => false,
            'visible'   => false
        ),
    'routes'        =>
        array(
            'name'      => __('routes'),
            'tiled'     => false,
            'visible'   => false
        ),
    'ranges'        =>
        array(
            'name'      => __('ranges'),
            'tiled'     => true,
            'visible'   => true
        ),
    'countries'     =>
        array(
            'name'      => __('countries'),
            'tiled'     => true,
            'visible'   => true
        ),
    'departements'  =>
        array(
            'name'      => __('departements/counties'),
            'tiled'     => true,
            'visible'   => true
        ),
    'maps'          =>
        array(
            'name'      => __('maps'),
            'tiled'     => true,
            'visible'   => false
        ),
    'outings'       =>
        array(
            'name'      => __('outings'),
            'tiled'     => true,
            'visible'   => false
        )
);
 
if (isset($displayed_layers))
{
    foreach($layers as &$value)
    {
        $value['visible'] = false;
    }
    foreach($displayed_layers as $layer)
    {
        $layers[$layer]['visible'] = true;
    }
}
else if (isset($undisplayed_layers))
{
    foreach($layers as &$value)
    {
        $value['visible'] = true;
    }
    foreach($undisplayed_layers as $layer)
    {
        $layers[$layer]['visible'] = false;
    }
}

$objects_to_mark = isset($document) ?
    ($document->get('id') . ':' . $document->get('geom_wkt')) : NULL;

if (!isset($search_url))
{
    $search_url = NULL;
}
if (!isset($container_div))
{
    $container_div = NULL;
}
if (!isset($tip_close))
{
    $tip_close = NULL;
}

echo show_map($search,                /* boolean indicating whether search is on or off */
              $layers,                /* array of layer descriptors */
              $objects_to_mark,       /* string representing the objects to mark */
              $search_url,            /* search url */
              $container_div,         /* the id of the container div */
              $tip_close              /* button tip string */
);

endif;
