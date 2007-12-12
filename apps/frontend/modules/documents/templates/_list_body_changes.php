<?php use_helper('History', 'SmartDate', 'SmartFormat');

$module_name = $item['archive']['module'];
$id = $item['document_id'];
$lang = $item['culture'];
$version = $item['version'];

// user can display his nickname, login_name, private_name
$user_name_to_use = $item['history_metadata']['user_private_data']['name_to_use'];
$link = '@document_by_id?module=users&id=' . $item['history_metadata']['user_private_data']['id'];
?>
<td>
<?php echo link_to($item['i18narchive']['name'], "@document_by_id_lang_version?module=$module_name&id=$id&lang=$lang&version=$version") ?>
</td><td>
<?php echo smart_date($item['created_at']) ?>
</td><td>
<?php if ($needs_username): ?>
    <?php echo link_to($item['history_metadata']['user_private_data'][$user_name_to_use], $link ) ?>
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
<td><em><?php echo smart_format($comment) ?></em></td>
<?php endif ?>
    