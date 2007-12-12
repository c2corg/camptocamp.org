<tr>
  <td><?php echo link_to_function(image_tag('/static/images/picto/rm.png',
                                            array('alt' => '-', 'title' => __('delete this condition level'))),
                                  "this.up('tr').remove()") ?></td>
  <?php foreach ($fields as $field): ?>
    <td><?php echo input_tag("conditions_levels[$level][$field]", 
                             !empty($data[$field]) ? $data[$field] : '') ?></td>
  <?php endforeach ?>
</tr>
