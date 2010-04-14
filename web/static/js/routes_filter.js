var activities = [];

// see hide_unrelated_fields() in routes.js
function hide_unrelated_filter_fields(current_activity)
{
    if (activities.indexOf(current_activity) != -1)
    {
         // if activity is already selected, unselect it
         activities = activities.without(current_activity);
    }
    else
    {
         // else add it to the selection
         activities.push(current_activity);
    }

    var show_flags =
    [
        'ski',
        'ski_snow_mountain',
        'ski_snow_mountain_rock',
        'ski_snow_mountain_rock_ice',
        'snow_ice',
        'snow_mountain_rock_ice',
        'rock_mountain',
        'hiking'
    ];
    var show_ski, show_ski_snow_mountain, show_ski_snow_mountain_rock, show_ski_snow_mountain_rock_ice,
        show_snow_ice, show_snow_mountain_rock_ice, show_rock_mountain, show_hiking;
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    activities.each(function(activity)
    {
        switch (activity)
        {
            case 1: // skitouring
                show_ski = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case 2: // snow_ice_mixed
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case 3: // mountain_climbing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case 4: // rock_climbing
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case 5: // ice_climbing
                show_ski_snow_mountain_rock_ice = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case 6: // hiking
                show_hiking = true;
        }
    });

    show_flags.each(function(flag)
    {
        var div_id = flag + '_fields';
        if (eval('show_' + flag))
        {
            $(div_id).show();
        }
        else
        {
            $(div_id).hide();
        }
    });
}
