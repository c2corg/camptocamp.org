/**
 * 0: splitter not displayed
 * 1: splitter will be displayed when timeout expires
 * 2: splitter displayed
 * 3: splitter will be hidden when timeout expires
 */
var splitter_status = 0;

/**
 * Hide or show an home section
 */
function toggleHomeSectionView(container_id, cookie_position, alt_up, alt_down)
{
    var div = $(container_id + '_section_container');
    var img = $(container_id + '_toggle');
    var title_div = $(container_id + '_section_title');
    var top_box = $(container_id).down('.nav_box_top');
    if (!div.visible())
    {
        img.title = alt_up;
        title_div.title = alt_up;
        new Effect.BlindDown(div, {duration:0.6});
        if (Prototype.Browser.IE &&
            (parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) <= 7))
        {
            div.style.display = 'block'; // for ie6-7 only
        }
        registerFoldStatus(container_id, cookie_position, true);
    }
    else
    {
        img.title = alt_down;
        title_div.title = alt_down;
        new Effect.BlindUp(div, {duration:0.6});
        registerFoldStatus(container_id, cookie_position, false);
    }
    if (top_box) {top_box.toggleClassName('small');}
}

/**
 * If user is logged, initiate ajax request to save pref in profile
 */
function registerFoldStatus(pref_name, cookie_position, opened)
{
    if ($('name_to_use') != null) { // logged user
         new Ajax.Request('/users/savepref', {
                          method: 'post',
                          parameters: {'name': pref_name + '_home_status', 'value': escape(opened) }
                          });
    }
    setFoldCookie(cookie_position, opened);
}

/**
 * Set a pref_value for 'fold' cookie
 */
function setFoldCookie(position, value)
{
    if (value) { value = 't'; } else { value = 'f'; }
    cookie_name = "fold=";
    date = new Date;
    date.setFullYear(date.getFullYear()+1);$
    // retrieve current cookie value
    var clen = document.cookie.length;
    var i = 0;
    var cookie_value = 'xxxxxxxxxxxxxxxxxxxx'; // size 20
    while (i < clen)
    {
        var j=i+cookie_name.length;
        if (document.cookie.substring(i, j)==cookie_name)
        {
            cookie_value = getCookieValue(j);
        }
        i=document.cookie.indexOf(" ",i)+1;
        if (i == 0) break;
    }
    // update position with value
    cookie_value = cookie_value.substr(0, position) +  value + cookie_value.substr(position+1);
    document.cookie = "fold=" + escape(cookie_value) + "; expires=" + date.toGMTString() + "; path=/";
}

function getCookieValue(offset)
{
    var endstr=document.cookie.indexOf (";", offset);
    if (endstr==-1) endstr=document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}

/**
 * This function is called during the page loading to hide a home section if needed
 */
function setHomeFolderStatus(container_id, position, default_opened, alt_down)
{
    var img = $(container_id + '_toggle');
    var title_div = $(container_id + '_section_title');
    var top_box = $(container_id).down('.nav_box_top');
    // retrieve cookie value if any
    var cookie_name = 'fold=';
    var clen = document.cookie.length;
    var i = 0;
    while (i < clen)
    {
        var j=i+cookie_name.length;
        if (document.cookie.substring(i, j)==cookie_name)
        {
            var opened = getCookieValue(j)[position];
            if (opened == 't')
            {
                return;
            }
            else if (opened == 'f')
            {
                $(container_id+'_section_container').hide();
                img.title = alt_down;
                title_div.title = alt_down;
                if (top_box) {top_box.addClassName('small');}
                return;
            }
            else
            {
                break;
            }
        }
        i=document.cookie.indexOf(" ",i)+1;
        if (i == 0) break;
    }
    //>>>>>>>>>>>>>>>>>>REMOVE FOLLOWING LINES AFTER NEXT UPDATE>>>>>>>>>>>>>>>>>>>>>
    // transition : we try to get cookie from container_id + "_home_status="
    // if it exists, erase it and save the pref in the new cookie
    var old_cookie_name = container_id + "_home_status=";
    i = 0;
    while (i < clen)
    {
        var j=i+old_cookie_name.length;
        if (document.cookie.substring(i, j)==old_cookie_name)
        {
            var opened = getCookieValue(j);
            if (opened == 'true')
            {
                setFoldCookie(position, 't')
                document.cookie = old_cookie_name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT';
                return;
            }
            else if (opened == 'false')
            {
                $(container_id+'_section_container').hide();
                img.title = alt_down;
                title_div.title = alt_down;
                if (top_box) {top_box.addClassName('small');}
                setFoldCookie(position, 'f')
                document.cookie = old_cookie_name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT';
                return;
            }
        }
        i=document.cookie.indexOf(" ",i)+1;
        if (i == 0) break;
    }
    //<<<<<<<<<<<<<<<<<<END OF LINES TO REMOVE<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    // no existing cookie_value
    if (default_opened == false)
    {
        $(container_id+'_section_container').hide();
        img.title = alt_down;
        title_div.title = alt_down;
        if (top_box) {top_box.addClassName('small');}
    }
}

/**
 * Add some properties and observers to have '+' and '-' pictos for folding sections
 */
function initHome()
{
    home_obj = $$('.nav_box_title', '.home_title');
    if (home_obj.length > 0)
    {
        home_obj.each(function(obj) {
            obj.observe('mouseover', function(e) {
                var img = obj.down();
                img.savedClass = $w(img.className)[1]; // the second class argument must be replaced
                img.removeClassName(img.savedClass);
                if (getContainer(obj).visible())
                {
                    img.addClassName('picto_close');
                }
                else
                {
                    img.addClassName('picto_open');
                }
            });
            obj.observe('mouseout', function(e) {
                var img = obj.down();
                img.removeClassName('picto_close');
                img.removeClassName('picto_open');
                img.addClassName(img.savedClass);
            });
        });
    }
}

function getContainer(obj)
{
    var cnId = obj.id;
    var prefix = cnId.substring(0, cnId.indexOf('_section_title'));
    return $(prefix + '_section_container');
}

/**
 * Hide or show a container
 */
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
    var activities = $$('#routes_section_container .title2');
    activities.each(function(a)
    {
        var activity_id = $w(a.className).last();
        showRoutes(activity_id);
    });
    window.location.href = '#routes_summary';
}

function hideAllRoutes()
{
    var activities = $$('#routes_section_container .title2');
    activities.each(function(a)
    {
        var activity_id = $w(a.className).last();
        hideRoutes(activity_id);
    });
}

function linkRoutes(activity_id)
{
    showRoutes(activity_id);
    var anchor = $$('#routes_section_container .title2.' + activity_id)[0].identify();
    window.location.href = '#' + anchor;
}

/**
 * This function is called to hide routes depending on their activities and the user prefs
 */
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

function toggleHomeNav(donotsavestatus)
{
    var wrapper = $('wrapper_context');
    var nav_box = $$('.nav_box');
    var splitter = $('splitter');

    var is_open = !wrapper.hasClassName('no_nav');

    if (is_open)
    {
        wrapper.addClassName('no_nav');
        nav_box.each(function(n) {n.hide();});
        splitter.title = open_close[2];
        splitter.addClassName('maximize');
    }
    else
    {
        wrapper.removeClassName('no_nav');
        nav_box.each(function(n) {n.show();});
        splitter.title = open_close[3];
        splitter.removeClassName('maximize');
    }
    unhighlight_splitter();

    if (donotsavestatus) {
      registerFoldStatus(nav_status_string, nav_status_cookie_position, !is_open);
    }
}

function toggleNav(donotsavestatus)
{
    var content_box = $('content_box');
    var splitter = $('splitter');
 
    var is_expanded = !content_box.hasClassName('wide');

    if (is_expanded)
    {
        if (content_box)
        {
            content_box.addClassName('wide');
        }
        if (splitter)
        {
            splitter.title = open_close[2];
            splitter.addClassName('maximize');
        }
    }
    else
    {
        if (content_box)
        {
            content_box.removeClassName('wide');
        }
        if (splitter)
        {
            splitter.title = open_close[3];
            splitter.removeClassName('maximize');
        }
    }

    if (splitter) unhighlight_splitter();

    if (donotsavestatus)
    {
        registerFoldStatus(nav_status_string, nav_status_cookie_position, !is_expanded);
    }
}

function setNav(is_home)
{
  // search for cookie
  var cookie_name = 'fold=';
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen)
  {
      var j=i+cookie_name.length;
      if (document.cookie.substring(i, j)==cookie_name)
      {
          var opened = getCookieValue(j)[nav_status_cookie_position];
          if (opened == 't')
          {
              return;
          }
          else if (opened == 'f')
          {
              if (is_home)
              {
                  toggleHomeNav(true);
              }
              else
              {
                  toggleNav(true);
              }
              return;
          }
          else
          {
              break;
          }
      }
      i=document.cookie.indexOf(" ",i)+1;
      if (i == 0) break;
    }
    // no cookie, use default
    if (!default_nav_status)
    {
        if (is_home)
        {
            toggleHomeNav(true);
        }
        else
        {
            toggleNav(true);
        }
    }
}

function highlight_splitter(ypos)
{
    var topleftcorner = $$('.ombre_haut_corner_left')[0];
    var bottomleftcorner = $$('.ombre_bas_corner_left')[0];

    $('splitter').addClassName('hl');
    topleftcorner.toggleClassName('hl');
    bottomleftcorner.toggleClassName('hl');

    var arrow = new Element('div', { id: 'splitter_arrow' });
    document.body.appendChild(arrow);
    set_splitter_pos(ypos);

    splitter_status = 2;
}

function move_splitter_arrow(e)
{
    set_splitter_pos(Event.pointerY(e));
}

function set_splitter_pos(ypos)
{
    var arrow = $('splitter_arrow');
    var splitter = $('splitter');
    var offset = splitter.cumulativeOffset();

    if (!arrow) return;

    if (splitter.hasClassName('maximize'))
    {
        arrow.addClassName('maximize');
        arrow.style.left = offset[0] + 20 + 'px';
    }
    else
    {
        arrow.style.left = offset[0] - 10 + 'px';
    }
    arrow.style.top = ypos + 'px';
}

function unhighlight_splitter()
{
    var topleftcorner = $$('.ombre_haut_corner_left')[0];
    var bottomleftcorner = $$('.ombre_bas_corner_left')[0];
    var splitter = $('splitter');

    if (!splitter.hasClassName('hl')) return;

    splitter.removeClassName('hl');
    topleftcorner.toggleClassName('hl');
    bottomleftcorner.toggleClassName('hl');

    if ($('splitter_arrow')) $('splitter_arrow').remove();

    splitter_status = 0;
}

// empty ajax feedback div
function emptyFeedback(aff)
{
    $(aff).innerHTML="";
    Element.hide($(aff));
}

// show form, show minus, hide plus
function showForm(form_id) 
{
    Element.show($(form_id + '_form'));
    Element.hide($(form_id + '_add'));
    Element.show($(form_id + '_hide'));
}

// hide form, hide minus, show plus
function hideForm(form_id)
{
    Element.hide($(form_id + '_form'));
    Element.hide($(form_id + '_hide'));
    Element.show($(form_id + '_add'));
}

// toggle select, form, minus, plus
function toggleForm(form_id)
{
    var association_content = $(form_id + '_association')
    
    if (association_content)
    {
        association_content.toggleClassName('hide');
        association_content.toggleClassName('show');
    }
}

function initObserve()
{
    var splitter = $('splitter');
    var splitter_timer = null;

    // handle splitter
    if (splitter)
    {
        if (splitter.up(1).hasClassName('home'))
        {
            splitter.observe('click', toggleHomeNav);
        }
        else
        {
            splitter.observe('click', toggleNav);
        }

        splitter.observe('mouseover', function(e)
        {
            switch (splitter_status)
            {
                case 3:
                    clearTimeout(splitter_timer);
                    splitter_status = 2;
                    break;
                case 0:
                    ypos = Event.pointerY(e); 
                    splitter_timer = setTimeout('highlight_splitter(ypos);', 200);
                    splitter_status = 1;
                    break;
                default:
                    break;
            }
        });
        splitter.observe('mouseout', function()
        {
            switch (splitter_status)
            {
                case 1:
                    clearTimeout(splitter_timer);
                    splitter_status = 0;
                    break;
                case 2:
                    splitter_timer = setTimeout('unhighlight_splitter();', 600);
                    splitter_status = 3;
                    break;
                default:
                    break;
            }
        });
        splitter.observe('mousemove', move_splitter_arrow);
    }

    // handle routes display
    var routes_section = $$('#routes_section_container .title2');
    if (routes_section.length > 0)
    {
        routes_section.each(function(t)
        {
            t.observe('click', handleRoutes);
        });
    }
    
    var close_routes = $$('#close_routes');
    if (close_routes.length > 0)
    {
        close_routes[0].observe('click', hideAllRoutes);
    }
    
    var open_routes = $$('#open_routes');
    if (open_routes.length > 0)
    {
        open_routes[0].observe('click', showAllRoutes);
    }
    
    var img_code = $$('input.code');
    if (img_code.length > 0)
    {
        img_code.each(function(code)
        {
            code.observe('click', code.select);
        });
    }
}

Event.observe(window, 'load', function()
{
    initHome();
    initObserve();
})
