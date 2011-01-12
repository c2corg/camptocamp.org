<?php use_helper('History', 'SmartDate', 'SmartFormat', 'sfBBCode');

$id = $item['document_id'];
$lang = $item['culture'];
$version = $item['version'];

$link = '@document_by_id?module=users&id=' . $item['history_metadata']['user_private_data']['id'];
?>
<td>
<?php echo link_to($item[$model_i18n]['name'], "@document_by_id_lang_version?module=$module_name&id=$id&lang=$lang&version=$version") ?>
</td><td>
<?php echo smart_date($item['created_at']) ?>
</td><td>
<?php if ($needs_username): ?>
    <?php echo link_to($item['history_metadata']['user_private_data']['topo_name'], $link ) ?>
    </td><td>
<?php endif ?>

<?php display_revision_nature($item['nature'], $item['history_metadata']['is_minor']);
if ($version > 1)
{
    printf(' (%s)', link_to(__('diff'), 
         "@document_diff?module=$module_name&id=$id&lang=$lang&new=$version&old=" . ($version - 1)));
    
} ?>
</td>

<?php if ($comment = $item['history_metadata']['comment']): ?>
<td><em><?php echo parse_bbcode_simple(smart_format($comment)) ?></em></td>
<?php endif ?>
    
