function toggleView(container_id, map)
{
    var div = $(container_id + '_section_container');
    var img_div = $('toggle_' + container_id);
    var section_title = $(container_id + '_section_title');
    var tip = $('tip_' + container_id);
    var alt_up = open_close[0];
    var alt_down = open_close[1];
    
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
        else new Effect.BlindDown(div, {duration:0.4});
    }
    else
    {
        img_div.removeClassName('picto_close');
        img_div.addClassName('picto_open');
        img_div.alt = '+';
        img_div.title = alt_down;
        tip.innerHTML = '[' + alt_down + ']';
        section_title.title = alt_down;
        new Effect.BlindUp(div, {duration:0.4});
    }
}

function toggleRoutes(activity_id)
{
    var div = $$('ul.act' + activity_id)[0];
    var img_div = $('act' + activity_id);
    
    if (!div.visible())
    {
        img_div.removeClassName('picto_open_light');
        img_div.addClassName('picto_close_light');
        div.style.height = '';
        new Effect.BlindDown(div, {duration:0.2});
    }
    else
    {
        img_div.removeClassName('picto_close_light');
        img_div.addClassName('picto_open_light');
        new Effect.BlindUp(div, {duration:0.2});
    }
}

function showRoutes(activity_id, activity)
{
    var div = $$('ul.act' + activity_id)[0];
    var img_div = $('act' + activity_id);
    
    if (!div.visible())
    {
        img_div.removeClassName('picto_open_light');
        img_div.addClassName('picto_close_light');
        div.style.height = '';
        new Effect.BlindDown(div, {duration:0.2});
    }
    window.location.href = '#' + activity + '_routes';
}

function initRoutes()
{
    var activities_to_show = $w($('quick_switch').className);
    if (activities_to_show.length != 0)
    {
        var routes = $$('.child_routes');
        if (routes.length != 0)
        {
            routes.each(function(r)
            {
                activity_id = $w(r.className).last();
                if (!activities_to_show.include(activity_id))
                {
                    var img_div = $(activity_id);
                    r.hide();
                    img_div.removeClassName('picto_close_light');
                    img_div.addClassName('picto_open_light');
                }
            });
        }
    }
}

Event.observe(window, 'load', function()
{
    initRoutes();
})