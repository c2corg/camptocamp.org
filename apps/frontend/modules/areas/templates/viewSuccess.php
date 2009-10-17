<?php
use_helper('Language', 'Sections', 'Viewer', 'General');

$id = $sf_params->get('id');
display_page_header('areas', $document, $id, $metadata, $current_version);

// lang-independent content starts here
echo start_section_tag('Information', 'data');
include_partial('data', array('document' => $document));
echo end_section_tag();

include_partial('documents/map_section', array('document' => $document,
                                               'displayed_layers'  => array()));
                                               
// lang-dependent content
echo start_section_tag('Description', 'description');
include_partial('documents/i18n_section', array('document' => $document, 'languages' => $sf_data->getRaw('languages'),
                                                'needs_translation' => $needs_translation, 'images' => $associated_images));
echo end_section_tag();

if (!$document->isArchive() && !$document->get('redirects_to'))
{
    echo start_section_tag('Linked documents', 'associated_docs');
    ?>
    <div class="col_left col_50">
    <ul class="children_lists">
        <?php
        $module_list = array('summits', 'routes', 'huts', 'parkings', 'sites', 'climbing_gym', 'maps', 'books');
        foreach ($module_list as $key => $module): ?><?php
            $criteria = "/$module/list?areas=$id";
            $picto = $module;
            
            if ($module == 'climbing_gym')
            {
                $criteria = "/sites/list?areas=$id&styp=12";
                $picto = 'sites';
            }
            ?>
            <li><?php echo picto_tag("picto_$picto") . ' ' . link_to(ucfirst(__($module)), $criteria); ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
    <div class="col_right col_50">
    <ul class="children_lists">
        <?php
        $module_list = array('outings', 'recent conditions', 'images', 'amateurs', 'pros', 'clubs');
        foreach ($module_list as $key => $module): ?><?php
            $criteria = "/$module/list?areas=$id";
            $picto = $module;
            
            if ($module == 'outings')
            {
                $criteria .= '&orderby=date&order=desc';
            }
            elseif ($module == 'recent conditions')
            {
                $criteria = "/outings/conditions?areas=$id&date=3W&orderby=date&order=desc";
                $picto = 'outings';
            }
            elseif ($module == 'amateurs')
            {
                $criteria = "/users/list?areas=$id&cat=1";
                $picto = 'users';
            }
            elseif ($module == 'pros')
            {
                $criteria = "/users/list?areas=$id&cat=2";
                $picto = 'users';
            }
            elseif ($module == 'clubs')
            {
                $criteria = "/users/list?areas=$id&cat=3";
                $picto = 'users';
            }
            ?>
            <li><?php echo picto_tag("picto_$picto") . ' ' . link_to(ucfirst(__($module)), $criteria); ?></li>
        <?php endforeach; ?>
    </ul>
    </div>
    <?php
    echo end_section_tag();
    
    include_partial('documents/images', array('images' => $associated_images,
                                              'document_id' => $id,
                                              'dissociation' => 'moderator',
                                              'is_protected' => $document->get('is_protected')));
}

include_partial('documents/license', array('license' => 'by-sa'));

echo end_content_tag();

include_partial('common/content_bottom');
?>
