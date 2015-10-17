<?php
use_helper('Field');

$is_connected = $sf_user->isConnected();
$is_moderator = $sf_user->hasCredential(sfConfig::get('app_credentials_moderator'));
?>
<ul class="data">
    <?php
    li(field_data_from_list_if_set($document, 'author_status', 'mod_xreports_author_status_list'));
    
    if ($is_connected && $is_moderator)
    {
        li(field_data_from_list_if_set($document, 'activity_rate', 'mod_xreports_activity_rate_list'));
        li(field_data_from_list_if_set($document, 'nb_outings', 'mod_xreports_nb_outings_list', array('title' => 'nb_outings_per_year')));
        li(field_data_from_list_if_set($document, 'autonomy', 'mod_xreports_autonomy_list'));
        li(field_data_if_set($document, 'age', array('suffix' => __('years'))));
        li(field_data_from_list_if_set($document, 'gender', 'mod_xreports_gender_list'));
        li(field_data_from_list_if_set($document, 'previous_injuries', 'mod_xreports_previous_injuries_list'));
    }
    ?>
</ul>
