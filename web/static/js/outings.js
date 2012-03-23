var max_elevation_old,
    height_diff_up_old,
    height_diff_down_old,
    access_elevation_old,
    up_snow_elevation_old,
    down_snow_elevation_old,
    height_diff_up_enable,
    height_diff_down_enable,
    up_snow_elevation_enable,
    down_snow_elevation_enable;

function switch_mw_contest_visibility()
{
    if ($('mw_contest') != undefined) {
        if ($('outing_with_public_transportation').checked)
        {
            $('mw_contest').show();
        }
        else
        {
            // hide mw div, uncheck contest checkbox
            $('mw_contest').hide();
            if ($('pseudo_id') == undefined && $('mw_contest_associate').checked)
            {
                // de-associated
                new Ajax.Request(
                  '/documents/removeAssociation/main_oc_id/' + $F('id') + '/linked_id/' + mw_contest_article_id + '/type/oc/strict/1',
                  {asynchronous:true,
                   evalScripts:false,
                   method:'post',
                   onComplete: function (request, json) {
                     Element.hide('indicator');
                   },
                   onFailure: function (request, json) {
                     Element.show('ajax_feedback_failure');
                     $('mw_contest_associate').checked = true;
                     setTimeout('emptyFeedback("ajax_feedback_failure")', 4000);
                   },
                   onLoading: function (request, json) {
                     Element.show('indicator');
                   },
                   onSuccess: function(request, json) {
                     $('mw_contest_associate').checked = false;
                     Element.hide('ajax_feedback_failure');
                   }
                  });
            }
        }
    }
}

function switch_mw_contest_association()
{
    if ($('pseudo_id') == undefined) {
        if ($('mw_contest_associate').checked)
        {
            // associate
            new Ajax.Request(
              '/outings/addAssociation/main_id/' + $F('id') + '/document_module/articles/document_id/' + mw_contest_article_id,
              {asynchronous:true,
               evalScripts:false,
               method:'post',
               onComplete: function (request, json) {
                 Element.hide('indicator');
               },
               onFailure: function (request, json) {
                 Element.show('ajax_feedback_failure');
                 $('mw_contest_associate').checked = false;
                 setTimeout('emptyFeedback("ajax_feedback_failure")', 4000);
               },
               onLoading: function (request, json) {
                 Element.show('indicator');
               },
               onSuccess: function(request, json) {
                 Element.hide('ajax_feedback_failure');
               }
              });
        }
        else
        {
            // de-associated
            new Ajax.Request(
              '/documents/removeAssociation/main_oc_id/' + $F('id') + '/linked_id/' + mw_contest_article_id + '/type/oc/strict/1',
              {asynchronous:true,
               evalScripts:false,
               method:'post',
               onComplete: function (request, json) {
                 Element.hide('indicator');
                 setTimeout('emptyFeedback("ajax_feedback_failure")', 4000);
               },
               onFailure: function (request, json) {
                 Element.show('ajax_feedback_failure');
               },
               onLoading: function (request, json) {
                 Element.show('indicator');
               }
              });
        }
    }
}



function hide_outings_unrelated_fields()
{
    var show_flags =
    [
        'outings_glacier',
        'outings_snow_elevation',
        'outings_track',
        'outings_conditions_levels',
        'outings_length',
        'outings_height_diff_down'
    ];
    var show_outings_glacier,
        show_outings_snow_elevation,
        show_outings_track,
        show_outings_conditions_levels,
        show_outings_length,
        show_outings_height_diff_down;

    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });

    var activities = $A($F($('activities')));
    activities.each(function(activity)
    {
        if (activity == 1 || activity == 2 || activity == 5 || activity == 7)
        {
            show_outings_snow_elevation = true;
            show_outings_track = true;
            show_outings_conditions_levels = true;
        }
        if (activity == 1 || activity == 2 || activity == 3 || activity == 7)
        {
            show_outings_glacier = true;
        }
        if (activity == 1 || activity == 6 || activity == 7)
        {
            show_outings_length = true;
            show_outings_height_diff_down = true;
        }
        else
        {
            if (Math.round($('outing_length').value) > 0)
            {
                show_outings_length = true;
            }
            if (Math.round($('height_diff_down').value) > 0)
            {
                show_outings_height_diff_down = true;
            }
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

function check_outing_activities(e)
{
    var activities_para = $A($F($('activities')));
    
    if (   activities_para.length == 1
        && activities_para[0] == 8
       )
    {
        alert(confirm_outing_paragliding_message);
        switchFormButtonsStatus($('editform'), false);
        return;
    }
    
    if (outing_activities_already_tested)
    {
        // no need to check activities twice
        return;
    }

    var activities = [];
    var i = 0;
    activities_para.each(function(activity)
    {
        if (activity != 8)
        {
            activities[i] = activity;
            i++;
        }
    });
    
    if (activities.length == 2)
    {
        activities.sortBy(function(s){return Math.round(s);});
        var act_0 = activities[0];
        var act_1 = activities[1];
        if ((act_0 == 2 && act_1 == 3) ||
            (act_0 == 2 && act_1 == 5) ||
            (act_0 == 2 && act_1 == 7) ||
            (act_0 == 3 && act_1 == 4) ||
            (act_0 == 3 && act_1 == 6) ||
            (act_0 == 4 && act_1 == 6))
        {
            // no need to check activities for these pairs
            return;
        }
    }
    
    // ask for confirmation if nb activities is > 1
    if ($('revision').value.length == 0 &&
        activities.length > 1 &&
        !confirm(confirm_outing_activities_message))
    {
        Event.stop(e);
        switchFormButtonsStatus($('editform'), false);
        outing_activities_already_tested = true;
    }
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
    if ($('revision').value.length == 0 &&
        year == now.getFullYear() &&
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

document.observe('dom:loaded', hide_outings_unrelated_fields);
document.observe('dom:loaded', switch_mw_contest_visibility);
document.observe('dom:loaded', function() {
    Event.observe('editform', 'submit', check_outing_activities);
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
