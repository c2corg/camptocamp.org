<?php use_helper('Forum'); ?>

<strong><?php
    echo link_to(
                $sf_user->getUsername(),
                '@document_by_id?module=users&id='.$sf_user->getId(),
                array('id' => 'name_to_use', 'class'=>'logged_as', 'title'=>__('Your are connected as '))
                )
?></strong>
| <?php echo link_to(__('Logout'), '@logout') ?>
| <?php echo f_link_to(image_tag('/static/images/picto/envelope.png', array(
                                                                    'alt' => __('MP'), 
                                                                    'title' => __('mailbox')
                                                                )
                                ), 'message_list.php'); ?>

<?php include_component('documents', 'getMsg'); ?>
