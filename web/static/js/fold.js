function toggleView(container_id, map, alt_up, alt_down)
{
    var div = $(container_id + '_section_container');
    var img = $('toggle_' + container_id);
    var section_title = $(container_id + '_section_title');
    
    if (!div.visible())
    {
        img.src = '/static/images/picto/close.png';
        img.alt = '-';
        img.title = alt_up;
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
        img.src = '/static/images/picto/open.png';
        img.alt = '+';
        img.title = alt_down;
        section_title.title = alt_down;
        new Effect.BlindUp(div, {duration:0.6});
    }
}
