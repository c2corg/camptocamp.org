<?php
    $options = array('multiple' => true, 'style' => 'height:100px;');

    echo start_group_tag(); // for the fieldset to be in a div of class form-row
    echo fieldset_tag('elevation');
    echo group_tag('Mini:', 'min_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_elevation', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('routes_quantity');
    echo group_tag('Mini:', 'min_routes_quantity', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_routes_quantity', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('min_rating');
    echo object_group_dropdown_tag(null, 'min_min_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Mini:');
    echo object_group_dropdown_tag(null, 'max_min_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Maxi:');
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('max_rating');
    echo object_group_dropdown_tag(null, 'min_max_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Mini:');
    echo object_group_dropdown_tag(null, 'max_max_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Maxi:');
    echo end_fieldset_tag();
    echo end_group_tag();

    // add a submit button and end section
    echo submit_tag(__('Search'), array('onclick' => 'do_search(this.form); return false;'));
    echo end_section_tag();

    // start section (includer will take care of ending it)
    echo start_section_tag('Detailed search criteria', 'search_form_detailed', 'closed', false);

    echo start_group_tag();
    echo fieldset_tag('mean_rating');
    echo object_group_dropdown_tag(null, 'min_mean_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Mini:');
    echo object_group_dropdown_tag(null, 'max_mean_rating', 'mod_sites_rock_free_ratings_list', null, false, 'Maxi:');
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('min_height');
    echo group_tag('Mini:', 'min_min_height', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_min_height', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('max_height');
    echo group_tag('Mini:', 'min_max_height', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_max_height', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo start_group_tag();
    echo fieldset_tag('mean_height');
    echo group_tag('Mini:', 'min_mean_height', 'input_tag', null, array('class' => 'short_input'));
    echo group_tag('Maxi:', 'max_mean_height', 'input_tag', null, array('class' => 'short_input'));
    echo end_fieldset_tag();
    echo end_group_tag();

    echo object_group_dropdown_tag(null, 'climbing_styles', 'mod_sites_climbing_styles_list', $options, false);
    echo object_group_dropdown_tag(null, 'rock_types', 'mod_sites_rock_types_list', $options, false);
    echo object_group_dropdown_tag(null, 'site_types', 'app_sites_site_types', $options, false);
    echo object_group_dropdown_tag(null, 'children_proof', 'mod_sites_children_proof_list', $options, false);
    echo object_group_dropdown_tag(null, 'rain_proof', 'mod_sites_rain_proof_list', $options, false);
    echo object_group_dropdown_tag(null, 'facings', 'mod_sites_facings_list', $options, false);
    echo object_group_dropdown_tag(null, 'equipment_rating', 'mod_sites_equipment_ratings_list', $options, false);
    // best_periods is a column in the sites database view but doesn't have an associated list in config/modules.yml
    //echo object_group_dropdown_tag(null, 'best_periods', 'mod_sites_best_periods_list', $options, false);
?>
