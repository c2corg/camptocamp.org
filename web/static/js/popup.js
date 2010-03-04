var delay = 6000;

var start_frame = 0;

function init_popup() {
    var lis = $('popup_slideimages');
    if (lis)
    {
        lis = lis.getElementsByTagName('li');
        lis[lis.length-1].show();
        end_frame = lis.length -1;
        start_slideshow(start_frame, end_frame, delay, lis);
    }
    
    var close_routes = $$('#close_popup_routes');
    if (close_routes.length > 0)
    {
        close_routes[0].observe('click', closePopupRoutes);
    }
    
    var open_routes = $$('#open_popup_routes');
    if (open_routes.length > 0)
    {
        open_routes[0].observe('click', openPopupRoutes);
    }
}

function start_slideshow(start_frame, end_frame, delay, lis) {
    setTimeout(fadeInOut(start_frame,start_frame,end_frame, delay, lis), delay);
}

function fadeInOut(frame, start_frame, end_frame, delay, lis) {
    return (function() {
        lis = $('popup_slideimages').getElementsByTagName('li');
        Effect.Fade(lis[frame]);
        if (frame == end_frame) { frame = start_frame; } else { frame++; }
        lisAppear = lis[frame];
        setTimeout("Effect.Appear(lisAppear);", 0);
        setTimeout(fadeInOut(frame, start_frame, end_frame, delay), delay + 1850);
    })
}

function handlePopupRoutes(up)
{
    var ctrl_div = $('size_ctrl');
    
    if (ctrl_div)
    {
        var desc_div = $$('.popup_desc')[0];
        var routes_div = $('routes_section_container');
        var close_div = $('close_popup_routes');
        var open_div = $('open_popup_routes');
        var close_status = close_div.visible();
        var open_status = open_div.visible();
        var old_level = 0;
        var level = 0;
        
        if (!close_status && open_status)
        {
            old_level = 0;
        }
        else if (close_status && open_status)
        {
            old_level = 1;
        }
        else if (close_status && !open_status)
        {
            old_level = 2;
        }
        
        if (up)
        {
            level = Math.min(2, old_level + 1);
        }
        else
        {
            level = Math.max(0, old_level - 1);
        }
        
        
        if (old_level == 1 && level == 0)
        {
            close_div.hide();
            desc_div.removeClassName('popup_iti');
            routes_div.hide();
        }
        else if (old_level == 0 && level == 1)
        {
            close_div.show();
            desc_div.addClassName('popup_iti');
            routes_div.show();
        }
        else if (old_level == 1 && level == 2)
        {
            open_div.hide();
            desc_div.hide();
            routes_div.addClassName('full');
        }
        else if (old_level == 2 && level == 1)
        {
            open_div.show();
            desc_div.show();
            routes_div.removeClassName('full');
        }
    }
}

function openPopupRoutes()
{
    handlePopupRoutes(1);
}
function closePopupRoutes()
{
    handlePopupRoutes(0);
}


Event.observe(window, 'load', init_popup, false);
