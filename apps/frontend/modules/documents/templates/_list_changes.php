<?php use_helper('History', 'SmartDate', 'SmartFormat', 'General', 'sfBBCode') ?>

<?php
$current_module = $sf_context->getModuleName();
if (!isset($model))
{
    $model = c2cTools::module2model($current_module);
}
$archive = $model;
$i18n_archive = $model . 'I18n';

if (count($items) == 0): ?>
    <p id="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul id="recent-changes">
    <?php 
    foreach ($items as $item): ?>
        <li><?php 
            $module_name = $item[$archive]['module'];
            $id = $item['document_id'];
            $lang = $item['culture'];
            $version = $item['version'];

            $link = '@document_by_id?module=users&id=' . $item['document_id'];

            echo picto_tag('picto_' . $module_name, __($module_name)),
                 ' ', link_to($item[$i18n_archive]['name'], "@document_by_id_lang_version?module=$module_name&id=$id&lang=$lang&version=$version"), ' - ',
                 smart_date($item['created_at']), ' - ';
            if ($needs_username)
            {
                echo link_to($item['history_metadata']['user_private_data']['topo_name'], $link ) . ' - ';
            }

            display_revision_nature($item['nature'], $item['history_metadata']['is_minor']);

            if ($version > 1)
            {
                printf(' (%s)', link_to(__('diff'), 
                                        "@document_diff?module=$module_name&id=$id&lang=$lang&new=$version&old=" . ($version - 1)));
            }
            if ($comment = $item['history_metadata']['comment']): ?>
                - <em><?php echo parse_bbcode_simple(smart_format($comment)) ?></em>
            <?php endif ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
