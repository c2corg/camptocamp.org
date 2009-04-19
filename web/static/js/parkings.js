function hide_parkings_unrelated_fields()
{
    value = $('public_transportation_rating').options[$('public_transportation_rating').selectedIndex].value;
    if(value != '3' && value != '0')
    {
        $('tp_types').show();
    }
    else
    {
        $('tp_types').hide();
    }
}

Event.observe(window, 'load', hide_parkings_unrelated_fields);