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
