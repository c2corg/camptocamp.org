<?php use_helper('Forum', 'General'); ?>

<strong><?php
echo link_to($sf_user->getUsername(),
             '@document_by_id?module=users&id='.$sf_user->getId(),
             array('id' => 'name_to_use', 'data-user-id' => $sf_user->getId(),
                   'class'=>'logged_as', 'title'=>__('Your are connected as ')));
?></strong>
 <?php echo link_to(picto_tag('action_cancel', __('Logout')), '@logout') ?> 
| <?php echo f_link_to(picto_tag('action_contact', __('mailbox')),
                       'message_list.php') . ' ';

include_component('documents', 'getMsg');
