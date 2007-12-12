function toggleView(container_id, map, alt_up, alt_down)
{
    var div = $(container_id + '_section_container');
    var img = $('toggle_'+container_id);

    if (!div.visible())
    {
        img.src = '/static/images/picto/close.png';
        img.alt = alt_up;
        img.title = img.alt;
        div.style.height = '';
        if (map && !map_initialized) {
            div.style.display = 'block';
            initialize_map();
        }
        else new Effect.BlindDown(div, {duration:0.6});
    }
    else
    {
        img.src = '/static/images/picto/open.png';
        img.alt = alt_down;
        img.title = img.alt;
        new Effect.BlindUp(div, {duration:0.6});
    }
}