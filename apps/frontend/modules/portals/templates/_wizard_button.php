<?php use_helper('Button') ?>
<div id="wizard_button">
    <div class="home_title_left picto_add"></div>
    <div class="home_title_text"><?php echo button_wizard($sf_user->isConnected() ?
                                                       null : array('title' => __('Create new outing unconnected'), 'url' => '@login')) ?></div>
</div>