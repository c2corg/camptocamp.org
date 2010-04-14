var max_elevation_old, height_diff_up_old, height_diff_down_old, access_elevation_old,
    up_snow_elevation_old, down_snow_elevation_old, height_diff_up_enable, height_diff_down_enable,
    up_snow_elevation_enable, down_snow_elevation_enable;

function hide_outings_unrelated_fields()
{
    var show_flags =
    [
        'outings_glacier',
        'outings_snow_elevation',
        'outings_track',
        'outings_conditions_levels',
        'outings_length'
    ];
    var show_outings_glacier, show_outings_snow_elevation, show_outings_track,
        show_outings_conditions_levels, show_outings_length;

    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    var activities = $A($F($('activities')));
    activities.each(function(activity)
    {
        // 1: skitouring, 2: snow_ice_mixed, 5: ice
        if (activity == 1 || activity == 2 || activity == 5)
        {
            show_outings_snow_elevation = true;
            show_outings_track = true;
            show_outings_conditions_levels = true;
        }
        if (activity == 1 || activity == 2 || activity == 3 || activity == 5)
        {
            show_outings_glacier = true;
        }
        if (activity == 1 || activity == 6)
        {
            show_outings_length = true;
        }
    });

    show_flags.each(function(flag)
    {
        if (eval('show_' + flag))
        {
            $(flag).show();
        }
        else
        {
            $(flag).hide();
        }
    });
}

function check_outing_date(e)
{
    if (outing_date_already_tested) 
    {
        // no need to check date twice
        return;
    }

    var year = $('date_year').value;
    var month = $('date_month').value;
    var day = $('date_day').value;

    var now = new Date();

    // ask for confirmation if outing date is today and if it is sooner than 14:00
    if (year == now.getFullYear() &&
        month == (now.getMonth() + 1) &&
        day == now.getDate() &&
        now.getHours() <= 14 &&
        !confirm(confirm_outing_date_message))
    {
        Event.stop(e);
        switchFormButtonsStatus($('editform'), false);
        outing_date_already_tested = true;
    }
}

Event.observe(window, 'load', hide_outings_unrelated_fields);
Event.observe(window, 'load', function() {
    Event.observe('editform', 'submit', check_outing_date);
});

function init_outings_var()
{
    max_elevation_old = Math.round($('max_elevation').value);
    height_diff_up_old = Math.round($('height_diff_up').value);
    height_diff_down_old = Math.round($('height_diff_down').value);
    access_elevation_old = Math.round($('access_elevation').value);
    up_snow_elevation_old = Math.round($('up_snow_elevation').value);
    down_snow_elevation_old = Math.round($('down_snow_elevation').value);
    
    height_diff_up_enable = true;
    height_diff_down_enable = true;
    up_snow_elevation_enable = true;
    down_snow_elevation_enable = true;
}

function update_max_elevation()
{
    if (height_diff_up_enable)
    {
        var max_elevation = Math.round($('max_elevation').value);
        height_diff_up_old = Math.round(height_diff_up_old + max_elevation - max_elevation_old);
        $('height_diff_up').value = height_diff_up_old;
        max_elevation_old = max_elevation;
    }
}

// Event.observe(window, 'load', init_outings_var);
