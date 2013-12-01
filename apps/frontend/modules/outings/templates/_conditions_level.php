<?php
use_helper('General');
?>
<tr>
  <td><?php echo link_to(picto_tag('picto_rm', __('delete this condition level')), '#',
                     array('class' => 'remove-condition-level')) ?></td>
  <?php foreach ($fields as $field): ?>
    <td><?php echo input_tag("conditions_levels[$level][$field]", $data[$field]) ?></td>
  <?php endforeach ?>
</tr>
