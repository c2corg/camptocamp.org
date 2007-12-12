function hide_outings_unrelated_fields()
{
    show_flags = new Array('outings_track', 'outings_conditions_levels');
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    activities = $A($F($('activities')));
    activities.each(function(activity)
    {
        // 1: skitouring, 2: snow_ice_mixed, 5: ice
        if (activity == 1 || activity == 2 || activity == 5)
        {
            show_outings_track = true;
            show_outings_conditions_levels = true;
            return;
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

Event.observe(window, 'load', hide_outings_unrelated_fields);
