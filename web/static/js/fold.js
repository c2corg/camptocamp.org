function toggleHomeSectionView(container_id, alt_up, alt_down)
{
    var div = $(container_id + '_section_container');
    var img = $(container_id + '_toggle');
    var title_div = $(container_id + '_section_title');
    if (!div.visible())
    {
      img.title = alt_up;
      title_div.title = alt_up;
      new Effect.BlindDown(div, {duration:0.6});
      if (Prototype.Browser.IE &&
          ((parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 6) ||
           (parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE")+5)) == 7))) {
        div.style.display = 'block'; // for ie6-7 only
      }
      registerHomeFoldStatus(container_id, true);
    }
    else
    {
      img.title = alt_down;
      title_div.title = alt_down;
      new Effect.BlindUp(div, {duration:0.6});
      registerHomeFoldStatus(container_id, false);
    }
}

function registerHomeFoldStatus(container_id, opened)
{
  if ($('name_to_use') != null) { // logged user
    var params = new Hash();
    params.name = container_id + '_home_status';
    params.value = escape(opened);
    new Ajax.Request('/users/savepref', {
                         method: 'post',
                         parameters: params
                     });
  }
  date = new Date;
  date.setFullYear(date.getFullYear()+1);
  document.cookie = container_id + "_home_status=" + escape(opened) + "; expires=" + date.toGMTString();
}

function getCookieValue(offset)
{
  var endstr=document.cookie.indexOf (";", offset);
  if (endstr==-1) endstr=document.cookie.length;
    return unescape(document.cookie.substring(offset, endstr));
}

function setHomeFolderStatus(container_id, default_opened, alt_down)
{
  var name = container_id + "_home_status=";
  var img = $(container_id + '_toggle');
  var title_div = $(container_id + '_section_title');
  var clen = document.cookie.length;
  var i = 0;
  while (i < clen)
  {
    var j=i+name.length;
    if (document.cookie.substring(i, j)==name) {
      var opened =  getCookieValue(j);
      if (opened == 'true')
      {
          return;
      }
      else if (opened == 'false')
      {
          $(container_id+'_section_container').hide();
          img.title = alt_down;
          title_div.title = alt_down;
          return;
      }
    }
    i=document.cookie.indexOf(" ",i)+1;
    if (i == 0) break;
  }
  if (default_opened == false) {
    $(container_id+'_section_container').hide();
    img.title = alt_down;
    title_div.title = alt_down;
  }
}

function initHome()
{
    home_obj = $$('.nav_box_title', '.home_title');
    if (home_obj.length > 0)
    {
        home_obj.each(function(obj){
         obj.observe('mouseover', function(e){
           var img = obj.down();
           img.savedClass = $w(img.className)[1]; // the second class argument must be replaced
           img.removeClassName(img.savedClass);
           if (getContainer(obj).visible()) {
             img.addClassName('picto_close');
           } else {
             img.addClassName('picto_open');
           }
         });
         obj.observe('mouseout', function(e){
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

function toggleHomeNav()
{
    var wrapper = $('wrapper_context');
    var nav_box = $$('.nav_box');
    var splitter = $$('.splitter')[0];
    
    if (nav_status)
    {
        wrapper.addClassName('no_nav');
        nav_box.each(function(n) {n.hide();});
        splitter.title = open_close[2];
        nav_status = false;
    }
    else
    {
        wrapper.removeClassName('no_nav');
        nav_box.each(function(n) {n.show();});
        splitter.title = open_close[3];
        nav_status = true;
    }
}

function toggleNav()
{
    var content = $$('.content_article');
    var tab = $$('.active_tab');
    var nav_tools = $$('#nav_tools');
    var nav_anchor = $$('#nav_anchor');
    var nav_space = $$('#nav_space');
    var splitter = $$('.splitter');
    
    if (nav_status)
    {
        if (content.length > 0)
        {
            content[0].addClassName('wide');
        }
        if (tab.length > 0)
        {
            tab[0].setStyle({'z-index': '9'});
        }
        if (nav_tools.length > 0)
        {
            nav_tools[0].addClassName('wide');
        }
        if (nav_anchor.length > 0)
        {
            nav_anchor[0].addClassName('wide');
        }
        if (nav_space.length > 0)
        {
            nav_space[0].setStyle({'width': '47px'});
        }
        if (splitter.length > 0)
        {
            splitter[0].title = open_close[2];
            splitter[0].setStyle({'cursor': 'e-resize'});
        }
        nav_status = false;
    }
    else
    {
        if (content.length > 0)
        {
            content[0].removeClassName('wide');
        }
        if (tab.length > 0)
        {
            tab[0].setStyle({'z-index': '11'});
        }
        if (nav_tools.length > 0)
        {
            nav_tools[0].removeClassName('wide');
        }
        if (nav_anchor.length > 0)
        {
            nav_anchor[0].removeClassName('wide');
        }
        if (nav_space.length > 0)
        {
            nav_space[0].setStyle({'width': '220px'});
        }
        if (splitter.length > 0)
        {
            splitter[0].title = open_close[3];
            splitter[0].setStyle({'cursor': 'w-resize'});
        }
        nav_status = true;
    }
}

function initObserve()
{
    splitter = $$('.home .splitter');
    if (splitter.length > 0)
    {
        splitter[0].observe('click', toggleHomeNav);
    }
    else
    {
        splitter = $$('.splitter');
        if (splitter.length > 0)
        {
            splitter[0].observe('click', toggleNav);
        }
        if (!nav_status)
        {
            nav_status = true;
            toggleNav();
        }
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

nav_status = true;

Event.observe(window, 'load', function()
{
    initHome();
    initRoutes();
    initObserve();
})