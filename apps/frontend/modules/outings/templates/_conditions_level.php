<?php
use_helper('General');
?>
<tr>
  <td><?php echo link_to_function(picto_tag('picto_rm', __('delete this condition level')),
                                  "this.up('tr').remove()") ?></td>
  <?php foreach ($fields as $field): ?>
    <td><?php echo input_tag("conditions_levels[$level][$field]", 
                             !empty($data[$field]) ? $data[$field] : '') ?></td>
  <?php endforeach ?>
</tr>
