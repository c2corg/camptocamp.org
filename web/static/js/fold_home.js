Event.observe(window, 'load', function(){
   $$('.nav_box_title', '.home_title').each(function(obj){
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

   function getContainer(obj) {
     var cnId = obj.id;
     var prefix = cnId.substring(0, cnId.indexOf('_section_title'));
     return $(prefix + '_section_container');
  }
});

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
