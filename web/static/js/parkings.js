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
}

Event.observe(window, 'load', hide_parkings_unrelated_fields);

function init_tp_desc()
{
    if(tp_empty)
    {
        $('public_transportation_description').value = tp_default;
        $('public_transportation_description').style.color = 'gray';
    }
}

Event.observe(window, 'load', init_tp_desc);

function hide_tp_default()
{
    if(tp_empty)
    {
        $('public_transportation_description').value = '';
        $('public_transportation_description').style.color = 'black';
        tp_empty = false;
    }
}

Event.observe(window, 'load', function() {
    Event.observe('editform', 'submit', hide_tp_default);
});
