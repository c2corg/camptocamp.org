<?php
use_helper('Diff', 'Date', 'Language', 'Viewer', 'WikiTabs', 'SmartFormat', 'sfBBCode');

$version = ($new_document->getVersion() != $current_version) ? $new_document->getVersion() : NULL;
$id = $sf_params->get('id');
$lang = $sf_params->get('lang');
$module = $sf_context->getModuleName();
$mobile_version = c2cTools::mobileVersion();

echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$new_document->get('name') : $new_document->get('name'), $module);

if (!$mobile_version)
{
    echo '<div id="nav_space">&nbsp;</div>';
    echo tabs_list_tag($id, $lang, 1, 'history', null, get_slug($new_document), $nb_comments);
}

echo display_content_top('doc_content');
echo start_content_tag($module . '_content');

?>
<p>
<?php
echo __('Diffing versions of %1% in %2%.',
        array('%1%' => isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$new_document->get('name') : $new_document->get('name'),
              '%2%' => format_language_c2c($new_document->getCulture())));
echo ' <strong>' . __('minor_tag') . '</strong> = ' . __('minor modification');
?>
</p>

<?php
$documents = array('old' => $old_document, 
                   'new' => $new_document);

$metadatas = array('old' => $old_metadata, 
                   'new' => $new_metadata);
?>

<table class="diff_metas">
  <tr>
  <?php foreach ($documents as $rank => $document): ?>
    <?php
    $metadata = $metadatas[$rank];
    
    $w_at = $metadata->get('written_at');
    $document_date = '<time datetime="' . date('c', strtotime($w_at)) . '">' . format_datetime($w_at) . '</time>';
    if ($document->getVersion() != $current_version)
    {
        $route = "@document_by_id_lang_version?module=$module&id=$id&lang=" . $document->getCulture() . '&version=' . $document->getVersion();
        $label = __('Version #%1%, date %2%', 
                    array('%1%' => $document->getVersion(),
                          '%2%' => $document_date));
    }
    else
    {
        $route = "@document_by_id_lang_slug?module=$module&id=$id&lang=" . $document->getCulture() . '&slug=' . get_slug($document);
        $label = __('Current version') . ' - ' . $document_date;
    }
    ?>  
    <td>
      <?php echo link_to($label, $route) ?>  
      <br />
      <?php echo __('by') . ' ' . link_to($metadata->get('user_private_data')->get('topo_name'), 
                                          '@document_by_id?module=users&id='. $metadata->get('user_id')) ?>
      <br />
      <?php if ($metadata->get('is_minor')): ?>
      <strong><?php echo __('minor_tag') ?></strong>
      <?php endif ?>
      <?php if (trim($metadata->get('comment'))): ?>
      <em>(<?php echo parse_bbcode_simple(smart_format(__($metadata->get('comment')))) ?>)</em>
      <?php endif ?>
      <br />
      <?php
      if ($rank == 'old' && $document->getVersion() > 1)
      {   
          echo link_to('&larr;&nbsp;' . __('previous difference'),
                       "@document_diff?module=$module&id=$id" .
                       '&lang=' . $document->getCulture() .
                       '&new=' . $document->getVersion() . 
                       '&old=' . ($document->getVersion() - 1));
      }   
      elseif ($rank == 'new' && $document->getVersion() != $current_version)
      {   
          echo link_to(__('next difference') . '&nbsp;&rarr;',
                       "@document_diff?module=$module&id=$id" .
                       '&lang=' . $document->getCulture() . 
                       '&new=' . ($document->getVersion() + 1) .
                       '&old=' . $document->getVersion());
      }   
      ?>  
    </td>
  <?php endforeach ?>
  </tr>
</table>

<?php show_documents_diff($old_document, $new_document, $fields, $module) ?>

<hr />
<h3>
<?php
if ($new_document->getVersion() != $current_version)
{
    $w_at = $new_metadata->get('written_at');
    echo __('Version #%1%, date %2%', array('%1%' => $new_document->getVersion(),
                                            '%2%' => '<time datetime="' . date('c', strtotime($w_at)) . '">' .
                                                     format_datetime($w_at) . '</time>'));
}
else
{
    echo __('Current version');
}
?>
</h3>

<div class="diff-section">
<div class="title"><?php echo __('Information') ?></div>
<div id="data_section_container" class="section">
<?php include_partial('data', array('document' => $new_document , 'preview' => true)); ?>
</div>
</div>

<div class="diff-section">
<div class="title"><?php echo __('Description') ?></div>
<div id="description_section_container" class="section">
<?php include_partial('i18n', array('document' => $new_document, 'needs_translation' => false,
                                    'associated_books' => null,
                                    'images' => $associated_images, 'filter_image_type' => false, 'preview' => true));
// rq: filter_image_type = false only taken into account by docs that can have both behaviour
?>
</div>
</div>
<?php
echo end_content_tag();

include_partial('common/content_bottom') ?>
