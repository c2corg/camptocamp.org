<?php
    $options = array('multiple' => true, 'style' => 'height:100px;');

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

    echo start_group_tag();
    echo fieldset_tag('height_diff_up');
    echo group_tag('Mini:', 'min_height_diff_up', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_height_diff_up', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo object_group_dropdown_tag(null, 'duration', 'mod_routes_durations_list', $options, false);
    echo object_group_dropdown_tag(null, 'activities', 'app_activities_list', $options, false);
    echo object_group_dropdown_tag(null, 'global_rating', 'app_routes_global_ratings', $options, false);

    // add a submit button and end section
    echo submit_tag(__('Search'), array('class' => 'picto action_filter', 'onclick' => 'do_search(this.form); return false;'));
    echo end_section_tag();

    // start section (includer will take care of ending it)
    echo start_section_tag('Detailed search criteria', 'search_form_detailed', 'closed', false);

    echo object_group_dropdown_tag(null, 'route_type', 'mod_routes_route_types_list', $options, false);

    echo start_group_tag();
    echo fieldset_tag('height_diff_down');
    echo group_tag('Mini:', 'min_height_diff_down', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_height_diff_down', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('route_length');
    echo group_tag('Mini:', 'min_route_length', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_route_length', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('difficulties_height');
    echo group_tag('Mini:', 'min_difficulties_height', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_difficulties_height', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo group_tag('is_on_glacier', 'is_on_glacier', 'checkbox_tag');

    echo object_group_dropdown_tag(null, 'facing', 'app_routes_facings', $options, false);
    echo object_group_dropdown_tag(null, 'configuration', 'mod_routes_configurations_list', $options, false);
    echo object_group_dropdown_tag(null, 'engagement_rating', 'app_routes_engagement_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'equipment_rating', 'app_equipment_ratings_list', $options, false);
    echo object_group_dropdown_tag(null, 'sub_activities', 'mod_routes_sub_activities_list', $options, false);
    echo object_group_dropdown_tag(null, 'toponeige_technical_rating', 'app_routes_toponeige_technical_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'toponeige_exposition_rating', 'app_routes_toponeige_exposition_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'labande_ski_rating', 'app_routes_labande_ski_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'labande_global_rating', 'app_routes_global_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'ice_rating', 'app_routes_ice_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'mixed_rating', 'app_routes_mixed_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'rock_free_rating', 'app_routes_rock_free_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'aid_rating', 'app_routes_aid_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'hiking_rating', 'app_routes_hiking_ratings', $options, false);
    echo object_group_dropdown_tag(null, 'snowshoeing_rating', 'app_routes_snowshoeing_ratings', $options, false);
?>
