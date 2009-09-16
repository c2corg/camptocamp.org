function toggleView(container_id, map, alt_up, alt_down)
{
    var div = $(container_id + '_section_container');
    var img_div = $('toggle_' + container_id);
    var section_title = $(container_id + '_section_title');
    var tip = $('tip_' + container_id);
    
    if (!div.visible())
    {
        img_div.removeClassName('picto_open');
        img_div.addClassName('picto_close');
        img_div.alt = '-';
        img_div.title = alt_up;
        tip.innerHTML = '[' + alt_up + ']';
        section_title.title = alt_up;
        div.style.height = '';
        if (map && !map_initialized) {
            div.style.display = 'block';
            Element.show('indicator');
            initialize_map();
            Element.hide('indicator');
        }
        else new Effect.BlindDown(div, {duration:0.6});
    }
    else
    {
        img_div.removeClassName('picto_close');
        img_div.addClassName('picto_open');
        img_div.alt = '+';
        img_div.title = alt_down;
        tip.innerHTML = '[' + alt_down + ']';
        section_title.title = alt_down;
        new Effect.BlindUp(div, {duration:0.6});
    }
}

function toggleRoutes(activity_id)
{
    var div = $('routes_' + activity_id);
    var img_div = $('activity_' + activity_id);
    
    if (!div.visible())
    {
        img_div.removeClassName('picto_open');
        img_div.addClassName('picto_close');
        div.style.height = '';
        new Effect.BlindDown(div, {duration:0.6});
    }
    else
    {
        img_div.removeClassName('picto_close');
        img_div.addClassName('picto_open');
        new Effect.BlindUp(div, {duration:0.6});
    }
}

