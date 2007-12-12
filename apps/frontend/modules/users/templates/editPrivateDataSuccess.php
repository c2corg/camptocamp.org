<div id="fake_div">
<?php 
    use_helper('Ajax','Language', 'Form', 'MyForm', 'Object', 'Javascript', 'Link');
    
    echo customization_nav('personal');

    // handle ajax errors
    echo ajax_feedback(true); // true == inline feedback
    // handle non ajax error form
    echo global_form_errors_tag();

    //echo form_tag('@user_edit');
    echo c2c_form_remote_tag('@user_edit');
?>
  <div id="customize">
    <?php
    echo fieldset_tag('Change your password');
    echo object_input_hidden_tag($user_private_data, 'getId') ;
    echo group_tag('new_password', 'password', 'input_password_tag', array('class' => 'medium_input'));
    echo group_tag('Retype your password:', 'new_password', 'input_password_tag', array('class' => 'medium_input'));
    echo end_fieldset_tag();
    
    echo fieldset_tag('Manage your email');
    echo object_group_tag($user_private_data, 'email', null, '', array('class' => 'long_input'));
    echo end_fieldset_tag();
    
    echo fieldset_tag('Select a name to use in guidebook') ?>
    <ul>
      <li><?php
          echo radiobutton_tag_selected_if('name_to_use[]', 'private_name',
                                           $user_private_data->get('name_to_use'));
          echo label_for('name_to_use_private_name', __('Fullname')) . ' (' . 
          input_tag('edit_full_name', $user_private_data->get('private_name'), array('class' => 'medium_input')) . ')';
      ?></li>
      <li><?php
          echo radiobutton_tag_selected_if('name_to_use[]', 'login_name',
                                           $user_private_data->get('name_to_use'));
          echo label_for('name_to_use_login_name',
                        __('Username') . ' (' . $user_private_data->getLoginName() . ')');
      ?></li>
      <li><?php
          echo radiobutton_tag_selected_if('name_to_use[]', 'username',
                                           $user_private_data->get('name_to_use'));
    
          $nickname_not_changed = $user_private_data->get('username') == $user_private_data->get('login_name');        
          $value = ($nickname_not_changed) ? $user_private_data->get('login_name') : $user_private_data->get('username');
    
          echo label_for('name_to_use_username', __('nickname')) . ' (' . 
               input_tag('edit_nickname', $value, array('class' => 'medium_input')) . ')'; 
          echo ' <span class="tips">(' . __('forum use your username or your nickname if it is set') . ')</span>';
      ?></li>
    </ul>
    <?php echo end_fieldset_tag() ?>
    
    <ul class="action_buttons">
      <li><?php echo submit_tag(__('Save'), array('class' => 'action_create')) ?></li>
    </ul>
  </div>

</form>
