function hide_parkings_unrelated_fields()
{
    value = $('public_transportation_rating').options[$('public_transportation_rating').selectedIndex].value;
    if(value != '3' && value != '0')
    {
        $('tp_types').show();
        $('tp_desc').show();
    }
    else
    {
        $('tp_types').hide();
        $('tp_desc').hide();
    }
    
    value = $('snow_clearance_rating').options[$('snow_clearance_rating').selectedIndex].value;
    if(value != '4' && value != '0')
    {
        $('snow_desc').show();
    }
    else
    {
        $('snow_desc').hide();
    }
}

Event.observe(window, 'load', hide_parkings_unrelated_fields);

function show_tp_default(en)
{
    if(tp_empty)
    {
        if(en)
        {
            $('public_transportation_description').value = tp_default;
            $('public_transportation_description').style.color = 'gray';
        }
        else
        {
            $('public_transportation_description').value = '';
            $('public_transportation_description').style.color = 'black';
        }
    }
}

function init_tp_desc()
{
    if($('public_transportation_description').value == '')
    {
        tp_empty = true;
        show_tp_default(true);
    }
    else
    {
        tp_empty = false;
    }
}

Event.observe(window, 'load', init_tp_desc);

function hide_tp_default()
{
    if(tp_empty)
    {
        show_tp_default(false);
        tp_empty = false;
    }
}

Event.observe(window, 'load', function() {
    Event.observe('editform', 'submit', hide_tp_default);
});
