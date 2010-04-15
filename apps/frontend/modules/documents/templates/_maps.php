<!-- map -->
<?php 
$document = isset($document) ? $document : null;

if ($document && !$document->get('geom_wkt')): ?>
    <div id="<?php echo $container_div ?>">
    <p class="default_text"><?php echo __('No geom info, please edit this document to add some');?></p>
    </div>
    <?php 
else:
use_helper('Map'); 

echo show_map($container_div, $document, $sf_user->getCulture());

endif;
