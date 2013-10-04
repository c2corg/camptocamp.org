(function(C2C) {

"use strict";

var activities = [];

C2C.update_on_select_change = function(field, optionIndex)
{
    var index = $(field + '_sel').value;
    if (index == '0' || index === ' ' || index == '-' || index >= 4)
    {
        $(field + '_span1').hide();
        $(field + '_span2').hide();
        if (optionIndex >= 4)
        {
            if (index == 4)
            {
                $(field + '_span3').show();
            }
            else
            {
                $(field + '_span3').hide();
            }
        }
    }
    else
    {
        $(field + '_span1').show();
        if (index == '~' || index == 3)
        {
            $(field + '_span2').show();
        }
        else
        {
            $(field + '_span2').hide();
        }
        if (optionIndex >= 4)
        {
            $(field + '_span3').hide();
        }
    }
};

function initialize_select()
{
    var re = new RegExp('_sel$', 'i');
    var sel_list = document.getElementsByTagName('SELECT');
    for (var i = 0; i < sel_list.length; ++i)
    {
        if (sel_list[i].id.search(re) != -1)
        {
            sel_list[i].onchange(true);
        }
    }
}

// see hide_unrelated_fields() in routes.js
C2C.hide_unrelated_filter_fields = function(current_activity)
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

    var show_flags = [
        'ski',
        'ski_snow_mountain',
        'ski_snow_mountain_rock',
        'ski_snow_mountain_rock_ice',
        'snow_ice',
        'snow_mountain_rock_ice',
        'snow_mountain_ice',
        'rock_mountain',
        'hiking',
        'snowshoeing'
    ];
    var show_ski, show_ski_snow_mountain, show_ski_snow_mountain_rock, show_ski_snow_mountain_rock_ice,
        show_snow_ice, show_snow_mountain_rock_ice, show_snow_mountain_ice, show_rock_mountain,
        show_hiking, show_snowshoeing;

    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });
    var show_snow = false;

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
                show_snow = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                show_snow_mountain_ice = true;
                break;

            case 3: // mountain_climbing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_snow_mountain_ice = true;
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
                show_snow_mountain_ice = true;
                break;

            case 6: // hiking
                show_hiking = true;
                break;

            case 7:
                show_snowshoeing = true;
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
    
    if (document.getElementById('conf') && show_ski_snow_mountain_rock)
    {
        var select_size = 6;
        if (show_snow)
        {
            $('conf').options[4].show();
            $('conf').options[5].show();
        }
        else
        {
            $('conf').options[4].hide();
            $('conf').options[5].hide();
            select_size -= 2;
        }
        
        $('conf').size = select_size;
    }
};

function initialize_activities()
{
    var act_form = document.getElementById('actform');
    var act_list = [];
    if(act_form)
    {
        act_list = act_form.getElementsByTagName("INPUT");
        for (var i = 0; i < act_list.length; ++i)
        {
            if(act_list[i].checked)
            {
                act_list[i].onclick(true);
            }
        }
    }
}

document.observe('dom:loaded', function()
{
    initialize_activities();
    initialize_select();
});

C2C.changeSelectSize = function(select_id, up_down)
{
    var height = $(select_id).offsetHeight;
    if(up_down)
    {
        height += 150; 
    }
    else
    {
        height = Math.max(100, height - 150);
    }
    $(select_id).style.height = height + "px";
};

})(window.C2C = window.C2C || {});
