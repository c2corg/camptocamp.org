<?php use_helper('History', 'SmartDate', 'SmartFormat') ?>

<?php $current_module = $sf_context->getModuleName(); ?>
<?php if (count($items) == 0): ?>
    <p id="recent-changes"><?php echo __('No recent changes available') ?></p>
<?php else: ?>
    <ul id="recent-changes">

    <?php foreach ($items as $item): ?>
        <li><?php 
            $module_name = $item['archive']['module'];
            $id = $item['document_id'];
            $lang = $item['culture'];
            $version = $item['version'];

            $link = '@document_by_id?module=users&id=' . $item['document_id'];

            echo image_tag('/static/images/modules/' . $module_name . '_mini.png', array('alt' => __($module_name),
                                                                                         'title' => __($module_name)));
            echo ' ';
            echo link_to($item['i18narchive']['name'], "@document_by_id_lang_version?module=$module_name&id=$id&lang=$lang&version=$version") . ' - ';
            echo smart_date($item['created_at']) . ' - ';
            if ($needs_username)
            {
                // user can display his nickname, login_name, private_name
                $user_name_to_use = $item['history_metadata']['user_private_data']['name_to_use'];
                echo link_to($item['history_metadata']['user_private_data'][$user_name_to_use], $link ) . ' - ';
            }

            display_revision_nature($item['nature'], $item['history_metadata']['is_minor']);

            if ($version > 1)
            {
                printf(' (%s)', link_to(__('diff'), 
                                        "@document_diff?module=$module_name&id=$id&lang=$lang&new=$version&old=" . ($version - 1)));
            }
            if ($comment = $item['history_metadata']['comment']): ?>
                - <em><?php echo smart_format($comment) ?></em>
            <?php endif ?>
            </li>
    <?php endforeach ?>
    </ul>
<?php endif ?>
