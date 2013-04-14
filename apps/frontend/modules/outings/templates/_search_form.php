<?php
    $options = array('multiple' => true, 'style' => 'height:100px;');

    /*
    // not useful "a priori" since already done for routes
    echo start_group_tag(); // for the fieldset to be in a div of class form-row
    echo fieldset_tag('min_elevation');
    echo group_tag('Mini:', 'min_min_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_min_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('max_elevation');
    echo group_tag('Mini:', 'min_max_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_max_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();
    */

    echo start_group_tag();
    echo fieldset_tag('height_diff_up');
    echo group_tag('Mini:', 'min_height_diff_up', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_height_diff_up', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo object_group_dropdown_tag(null, 'activities', 'app_activities_list', $options, false);

    echo start_group_tag();
    echo fieldset_tag('Period:');
    echo group_tag('From:', 'date_from', 'input_date_tag', null, array(
        'use_short_month'   => true,
        'html'              => array('style' => 'width:75px;')
    ));
    echo group_tag('To:', 'date_to', 'input_date_tag', null, array(
        'use_short_month'   => true,
        'html'              => array('style' => 'width:75px;')
    ));
    echo end_fieldset_tag();
    echo end_group_tag();

    // add a submit button and end section
    echo c2c_submit_tag(__('Search'), array('picto' => 'action_filter', 'onclick' => 'C2C.do_search(this.form); return false;'));
    echo end_section_tag();

    // start section (includer will take care of ending it)
    echo start_section_tag('Detailed search criteria', 'search_form_detailed', 'closed', false);

    echo start_group_tag();
    echo fieldset_tag('height_diff_down');
    echo group_tag('Mini:', 'min_height_diff_down', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_height_diff_down', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('outing_length');
    echo group_tag('Mini:', 'min_outing_length', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_outing_length', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo object_group_dropdown_tag(null, 'hut_status', 'mod_outings_hut_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'frequentation_status', 'mod_outings_frequentation_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'conditions_status', 'mod_outings_conditions_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'access_status', 'mod_outings_access_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'lift_status', 'mod_outings_lift_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'glacier_status', 'mod_outings_glacier_statuses_list', $options, false);
    echo object_group_dropdown_tag(null, 'track_status', 'mod_outings_track_statuses_list', $options, false);
    // condition_levels is in config/module.yml but there's no associated column in the outings database view
    //echo object_group_dropdown_tag(null, 'condition_level', 'mod_outings_condition_levels_list', $options, false);
?>
