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
    echo group_tag('new_password', 'password', 'input_password_tag', null, array('class' => 'medium_input'));
    echo group_tag('Retype your password:', 'new_password', 'input_password_tag', null, array('class' => 'medium_input'));
    echo end_fieldset_tag();
    
    echo fieldset_tag('Manage your email');
    echo object_group_tag($user_private_data, 'email', null, '', array('class' => 'long_input'));
    echo end_fieldset_tag();
    
    echo fieldset_tag('Manage your private data') ?>
    <div class="form-row">
    <ul>
      <li><?php
          echo label_for('topo_name', __('topoName_desc'), array('class' => 'fieldname')) . ' ' . 
          input_tag('edit_topo_name', $user_private_data->get('topo_name'), array('class' => 'medium_input'));
      ?></li>
      <li><?php
          echo label_for('username', __('nickName_desc'), array('class' => 'fieldname')) . ' ' .
               input_tag('edit_nickname', $user_private_data->get('username'), array('class' => 'medium_input'));
      ?></li>
      <li><?php
          echo label_for('login_name', __('LoginName_desc'), array('class' => 'fieldname')) . ' <strong>' . $user_private_data->getLoginName() . '</strong>';
      ?></li>
    </ul>
    <?php echo 
    end_fieldset_tag();

    echo fieldset_tag('Manage your profile page');
    echo object_group_tag($user_private_data, 'is_profile_public', 'object_checkbox_tag');
    echo end_fieldset_tag() ?>
    
    <ul class="action_buttons">
      <li><?php echo submit_tag(__('Save'), array('class' => 'action_create')) ?></li>
    </ul>
    </div>
  </div>

</form>
