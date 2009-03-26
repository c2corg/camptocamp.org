function hide_unrelated_fields()
{
    show_flags = new Array('ski', 'ski_snow', 'snow_ice', 'rock_mountain', 'hiking', 'ski_snow_ice', 'ski_snow_mountain_rock',
                           'ski_snow_mountain_rock_ice', 'snow_mountain_rock_ice', 'ski_snow_mountain');
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    activities = $A($F($('activities')));
    activities.each(function(activity)
    {
        switch (activity)
        {
            case '1': // skitouring
                show_ski = true;
                show_ski_snow = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case '2': // snow_ice_mixed
                show_snow_ice = true;
                show_ski_snow = true;
                show_ski_snow_mountain_rock = true;
                show_snow_mountain_rock_ice = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case '3': // mountain_climbing
                show_rock_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_snow_mountain_rock_ice = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case '4': // rock_climbing
                show_rock_mountain = true;
                show_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case '5': // ice_climbing
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case '6': // hiking
                show_hiking = true;
        }
    });

    show_flags.each(function(flag)
    {
        div_id = flag + '_fields';
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

Event.observe(window, 'load', hide_unrelated_fields);
