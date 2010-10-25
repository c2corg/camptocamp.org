<?php 
use_helper('Button', 'Ajax', 'Javascript');

$module = $sf_context->getModuleName();
?>
<div id="nav_tools" class="nav_box">
    <div id="nav_tools_top"></div>
    <div id="nav_tools_content">
        <ul>
            <li><?php echo button_back($module) ?></li>
        </ul>
    </div>
    <div id="nav_tools_down"></div>
</div>
