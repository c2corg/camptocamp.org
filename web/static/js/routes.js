(function(C2C) {

"use strict";

C2C.hide_unrelated_fields = function()
{
    var show_data, show_ski, show_ski_snow, show_ski_snow_mountain, show_ski_snow_mountain_rock,
        show_ski_snow_mountain_rock_ice, show_ski_snow_mountain_hiking, show_snow_ice,
        show_rock_mountain, show_snow_mountain_rock_ice, show_snow_mountain_ice, show_hiking,
        show_hiking2, show_pack_ski, show_pack_snow_easy, show_pack_mountain_easy,
        show_pack_rock_bolted, show_pack_ice, show_pack_hiking, show_snowshoeing;

    var show_flags = [
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
        'snow_mountain_ice',
        'hiking',
        'hiking2',
        'pack_ski',
        'pack_snow_easy',
        'pack_mountain_easy',
        'pack_rock_bolted',
        'pack_ice',
        'pack_hiking',
        'snowshoeing'
    ];
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });
    var show_snow = false;

    var activities = $A($F($('activities')));
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
                show_pack_ski = true;
                break;

            case '2': // snow_ice_mixed
                show_ski_snow = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_hiking = true;
                show_snow = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                show_snow_mountain_ice = true;
                show_pack_snow_easy = true;
                show_pack_ice = true;
                break;

            case '3': // mountain_climbing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_ski_snow_mountain_hiking = true;
                show_snow_mountain_rock_ice = true;
                show_snow_mountain_ice = true;
                show_rock_mountain = true;
                show_pack_mountain_easy = true;
                break;

            case '4': // rock_climbing
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                show_pack_rock_bolted = true;
                break;

            case '5': // ice_climbing
                show_ski_snow_mountain_rock_ice = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                show_snow_mountain_ice = true;
                show_pack_ice = true;
                break;

            case '6': // hiking
                show_ski_snow_mountain_hiking = true;
                show_hiking = true;
                show_hiking2 = true;
                show_pack_hiking = true;
                break;
            case '7': // snowshoeing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_snowshoeing = true;
                show_ski_snow = true;
                break;
            default :
                show_data = false;
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
    
    if (show_ski_snow_mountain_rock)
    {
        var select_size = 7;
        if (show_snow)
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
    
    var line_button = $$('input.rlineb');
    if (line_button.length > 0)
    {
        line_button.each(function(button)
        {
            if (show_snow_mountain_rock_ice)
            {
                button.show();
            }
            else
            {
                button.hide();
            }
        });
    }
};

document.observe('dom:loaded', C2C.hide_unrelated_fields);

})(window.C2C = window.C2C || {});
