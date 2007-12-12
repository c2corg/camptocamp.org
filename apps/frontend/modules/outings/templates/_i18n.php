<?php
use_helper('sfBBCode', 'SmartFormat', 'Field');

$conditions = $document->get('conditions');
$conditions_levels = $document->getRaw('conditions_levels');

// hide condition levels if ski, snow or ice_climbing are not among outing activities
if (!array_intersect(array(1,2,5), $document->getRaw('activities')))
{
    $conditions_levels = NULL;
}

if (!empty($conditions) || !empty($conditions_levels)):
?>
    <div class="section_subtitle" id="_conditions"><?php echo __('conditions') ?></div>
    <?php
    $conditions_levels = $document->get('conditions_levels');
    if (!empty($conditions_levels) && count($conditions_levels)):
        $level_fields = sfConfig::get('mod_outings_conditions_levels_fields');
        ?>
        <table id="conditions_levels_table">
          <?php foreach ($level_fields as $field): ?>
          <colgroup id="<?php echo $field ?>"></colgroup>
          <?php endforeach ?>
          <thead>
            <tr>
            <?php foreach ($level_fields as $field): ?>
              <th><?php echo __($field) ?></th>
            <?php endforeach ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($conditions_levels as $level => $data): ?>
              <tr>
                <?php foreach ($level_fields as $field): ?>
                  <td><?php echo $data[$field] ?></td>
                <?php endforeach ?>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
    <?php endif ?>
    <p><?php echo parse_links(parse_bbcode($conditions)) ?></p>
<?php
endif;

echo field_text_data_if_set($document, 'weather');
echo field_text_data_if_set($document, 'participants');
echo field_text_data_if_set($document, 'timing');
echo field_text_data_if_set($document, 'description', 'comments');
echo field_text_data_if_set($document, 'access_comments');
echo field_text_data_if_set($document, 'hut_comments');
?>
