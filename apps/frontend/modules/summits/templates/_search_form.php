<?php
    echo start_group_tag(); // for the fieldset to be in a div of class form-row
    echo fieldset_tag('elevation');
    echo group_tag('Mini:', 'min_elevation');
    echo group_tag('Maxi:', 'max_elevation');
    echo end_fieldset_tag();
    echo end_group_tag();
?>
