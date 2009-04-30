function hide_unrelated_fields()
{
    show_flags = new Array
    (
        'data',
        'ski',
        'ski_snow',
        'ski_snow_mountain',
        'ski_snow_mountain_rock',
        'ski_snow_mountain_rock_ice',
        'ski_snow_mountain_hiking',
        'snow_ice',
        'rock_mountain',
        'snow_mountain_rock_ice',
        'hiking',
        'hiking2'
    );
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    activities = $A($F($('activities')));
    if(activities.length > 0)
    {
        show_data = true;
    }
    activities.each(function(activity)
    {
        switch (activity)
        {
            case '1': // skitouring
                show_ski = true;
                show_ski_snow = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_hiking = true;
                break;

            case '2': // snow_ice_mixed
                show_ski_snow = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_hiking = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case '3': // mountain_climbing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_hiking = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case '4': // rock_climbing
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case '5': // ice_climbing
                show_ski_snow_mountain_rock_ice = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case '6': // hiking
                show_ski_snow_mountain_hiking = true;
                show_hiking = true;
                show_hiking2 = true;
                break;
            
            default :
                show_data = false;
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
    
    if (show_ski_snow_mountain_rock)
    {
        select_size = 7;
        if (show_snow_mountain_rock_ice)
        {
            $('configuration').options[1].show();
        }
        else
        {
            $('configuration').options[1].hide();
            select_size -= 1;
        }
        
        if (show_snow_ice)
        {
            $('configuration').options[5].show();
            $('configuration').options[6].show();
        }
        else
        {
            $('configuration').options[5].hide();
            $('configuration').options[6].hide();
            select_size -= 2;
        }
        
        $('configuration').size = select_size;
    }
}

Event.observe(window, 'load', hide_unrelated_fields);
