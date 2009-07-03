<?php
use_helper('Date', 'History', 'MyForm', 'Language', 'Viewer', 'WikiTabs', 'SmartFormat', 'sfBBCode');

$static_base_url = sfConfig::get('app_static_url');
use_javascript($static_base_url . '/static/js/diff.js?' . sfSVN::getHeadRevision('diff.js'), 'last');
use_javascript($static_base_url . '/static/js/history_tools.js?' . sfSVN::getHeadRevision('history_tools.js'), 'last');

$module = $sf_context->getModuleName();
$lang = $sf_params->get('lang');
$id = $sf_params->get('id');
$table_list_even_odd = 0;
$slug = formate_slug($document['i18narchive']['search_name']);

echo display_title(isset($title_prefix) ? $title_prefix.__('&nbsp;:').' '.$document_name : $document_name, $module);
echo '<div id="nav_space">&nbsp;</div>';
echo tabs_list_tag($id, $lang, $exists_in_lang, 'history', null, $slug);
?>

<div id="wrapper_context">
<div id="ombre_haut">
    <div id="ombre_haut_corner_right"></div>
    <div id="ombre_haut_corner_left"></div>
</div>

<div id="content_article">
<div id="article" class="article <?php echo $module . '_content'; ?>">

<p><?php echo __('Viewing history from %1% in %2%',
                  array('%1%' => $document_name,
                        '%2%' => format_language_c2c($lang))) ?>
</p>

<p><?php echo __('Legend:') .
              ' * = ' . __('current version') .
              ', <strong>' . __('minor_tag') . '</strong> = ' . __('minor modification') ?>
</p>

<p><?php
echo form_tag("@document_diff_post?module=$module&id=$id&lang=$lang", 
              array('method' => 'post'));

$versions_nb = count($versions);

$submit_options = array('title' => __('Show differences between selected versions'),
                        'value' => __('Compare'),
                        'class' => 'picto action_filter single_button');

?></p>

<p><?php
echo label_tag('minor_revision_checkbox', __('hide minor revisions'));
echo checkbox_tag('minor_revision_checkbox', '1', false, array('onclick' => 'toggle_minor_revision();'));
?>
</p>

<?php echo compare_submit($versions_nb, $submit_options) ?>


<table id="pagehistory"">

  <?php if ($versions_nb != 1):?>
  <col />
  <col class="radio_col" />
  <?php endif ?>
  
  <tr>
    <th><?php echo __('version') ?></th>
    <?php if ($versions_nb != 1):?><th>&nbsp;</th><?php endif ?>
    <th><?php echo __('created_at') ?></th>
    <th><?php echo __('rev author') ?></th>
    <th><?php echo __('rev nature') ?></th>
    <th><?php echo __('comment') ?></th>
  </tr>

<?php 
$row_nb = 1;
foreach ($versions as $version):
  $table_class = ($table_list_even_odd % 2 == 0) ? 'table_list_even' : 'table_list_odd';
  if ($version['version'] == $current_version)
  {
      $current_label = '*';
      $view_link = "@document_by_id_lang_slug?module=$module&id=" . $version['document_id'] .
                   '&lang=' . $version['i18narchive']['culture'] . '&slug=' . $slug;
  }
  else
  {
      $current_label = '';
      $view_link = "@document_by_id_lang_version?module=$module&version=" . $version['version'] . '&id=' .
                   $version['document_id'] . '&lang=' . $version['i18narchive']['culture'];
  }

  ?>
  <tr class="<?php echo $table_class; if($version['history_metadata']['is_minor']) echo ' minor_revision'; ?>">
    <td><?php echo link_to($version['version'] . $current_label, $view_link) ?></td>
    <?php if ($versions_nb != 1):?>
    <td class="history_buttons"><?php echo radiobuttons_history_tag($row_nb++, $version['version']) ?></td>
    <?php endif ?>
    <td><?php echo format_datetime($version['created_at']) ?></td>

    <td><?php 
    echo link_to($version['history_metadata']['user_private_data']['topo_name'],
                           'users/view?id=' . $version['history_metadata']['user_private_data']['id']) ?></td>
    <td><?php display_revision_nature($version['nature'],
                                      $version['history_metadata']['is_minor']) ?></td>
    <td><?php echo parse_bbcode_simple(smart_format($version['history_metadata']['comment'])) ?></td>
  </tr>
<?php $table_list_even_odd++; ?>
<?php endforeach ?>
</table>

<?php echo compare_submit($versions_nb, $submit_options) ?>
</form>

</div></div>

<?php include_partial('common/content_bottom')?>
