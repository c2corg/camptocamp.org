function toggleView(container_id, map)
{
    var div = $(container_id + '_section_container');
    var img_div = $('toggle_' + container_id);
    var section_title = $(container_id + '_section_title');
    var tip = $('tip_' + container_id);
    var alt_up = open_close[1];
    var alt_down = open_close[0];
    
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
    var div = $$('ul.' + activity_id)[0];
    var img_div = $(activity_id);
    
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

function handleRoutes(event)
{
    var activity_id = $w(this.className).last();
    toggleRoutes(activity_id);
}

function showRoutes(activity_id)
{
    var div = $$('ul.' + activity_id)[0];
    var img_div = $(activity_id);
    
    if (!div.visible())
    {
        img_div.removeClassName('picto_open_light');
        img_div.addClassName('picto_close_light');
        div.style.height = '';
        new Effect.BlindDown(div, {duration:0.2});
    }
}

function hideRoutes(activity_id)
{
    var div = $$('ul.' + activity_id)[0];
    var img_div = $(activity_id);
    
    if (div.visible())
    {
        img_div.removeClassName('picto_close_light');
        img_div.addClassName('picto_open_light');
        new Effect.BlindUp(div, {duration:0.2});
    }
}

function showAllRoutes()
{
    activities = $$('#routes_section_container .title2');
    activities.each(function(a)
    {
        activity_id = $w(a.className).last();
        showRoutes(activity_id);
    });
    window.location.href = '#routes_summary';
}

function hideAllRoutes()
{
    activities = $$('#routes_section_container .title2');
    activities.each(function(a)
    {
        activity_id = $w(a.className).last();
        hideRoutes(activity_id);
    });
}

function linkRoutes(activity_id)
{
    showRoutes(activity_id);
    anchor = $$('#routes_section_container .title2.' + activity_id)[0].identify();
    window.location.href = '#' + anchor;
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

function toggleNav()
{
    content = $$('.content_article')[0];
    tab = $$('.active_tab')[0];
    nav_tools = $('nav_tools');
    nav_anchor = $('nav_anchor');
    nav_space = $('nav_space');
    splitter = $$('.splitter')[0];
    
    if (nav_status)
    {
        content.addClassName('wide');
        tab.setStyle({'z-index': '9'});
        nav_tools.addClassName('wide');
        nav_anchor.addClassName('wide');
        nav_space.setStyle({'width': '47px'});
        splitter.title = open_close[2];
        nav_status = false;
    }
    else
    {
        content.removeClassName('wide');
        tab.setStyle({'z-index': '11'});
        nav_tools.removeClassName('wide');
        nav_anchor.removeClassName('wide');
        nav_space.setStyle({'width': '220px'});
        splitter.title = open_close[3];
        nav_status = true;
    }
}

function initObserve()
{
    splitter = $$('.splitter');
    if (splitter.length > 0)
    {
        splitter[0].observe('click', toggleNav);
    }
    
    routes_section = $$('#routes_section_container .title2');
    if (routes_section.length > 0)
    {
        routes_section.each(function(t)
        {
            t.observe('click', handleRoutes);
        });
    }
    
    close_routes = $$('#close_routes');
    if (close_routes.length > 0)
    {
        close_routes[0].observe('click', hideAllRoutes);
    }
    open_routes = $$('#open_routes');
    
    if (open_routes.length > 0)
    {
        open_routes[0].observe('click', showAllRoutes);
    }
}

Event.observe(window, 'load', function()
{
    initRoutes();
    initObserve();
})