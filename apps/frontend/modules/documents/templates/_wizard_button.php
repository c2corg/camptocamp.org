<?php use_helper('Button') ?>
<div id="wizard_button" class="nav_box">
    <div id="wizard_button_top"></div>
    <div id="wizard_button_content">
        <div class="wizard_title">
            <div class="nav_box_image picto_add"></div>
            <div class="nav_box_title_text"><?php echo button_wizard($sf_user->isConnected() ?
                                                       null : array('title' => __('Create new outing unconnected'), 'url' => '@login_redirect?outings_wizard')) ?></div>
        </div>
    </div>
    <div id="wizard_button_down"></div>
</div>
