<?php
use_helper('Date', 'Language', 'SmartFormat', 'sfBBCode');

//$id = $document->getId();
$module = $sf_context->getModuleName();
?>

<p class="important_message">
<?php echo __('Warning: archived version:') . ' ' .
           __('version %1%', array('%1%' => $document->getVersion())) .
           ' (' . format_language_c2c($document->getCulture())  . ')' .
           ' - ' . format_datetime($metadata->get('written_at')) . ' ' .
           __('by') . ' ' . link_to($metadata->get('user_private_data')->get('topo_name'),
                                '@document_by_id?module=users&id='. $metadata->get('user_id')) ?>
<br />
<?php
if ($metadata->get('is_minor'))
{
    $change_comment = '<strong>' . __('minor modification') . '</strong>';
}
else
{
    $change_comment = '';
}
if (trim($metadata->get('comment')))
{
    if (!$metadata->get('is_minor'))
    {
        $change_comment = '<strong>' . __('modification') . '</strong>';
    }
    $change_comment .= __(' :') . ' '
                    . parse_bbcode_simple(smart_format(__($metadata->get('comment'))));
}
echo $change_comment;
?>
<br />
    <?php 
    if ($document->getVersion() > 1)
    {
        echo link_to('&laquo; ' . __('version %1%', array('%1%' => $document->getVersion() - 1)),
                     "@document_by_id_lang_version?module=$module&id=" . $id . '&version=' . ($document->getVersion() - 1) .
                     '&lang=' . $document->getCulture());
        echo ' | ';
    }
    
    echo link_to(__('current version'),
                 "@document_by_id_lang?module=$module&id=" . $id . '&lang=' . $document->getCulture());
    
    echo ' | ';
    echo link_to(__('version %1%', array('%1%' => $document->getVersion() + 1)) . ' &raquo;',
                 "@document_by_id_lang_version?module=$module&id=" . $id . '&version=' . ($document->getVersion() + 1) .
                 '&lang=' . $document->getCulture());
    ?>
</p>
