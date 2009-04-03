Event.observe(window, 'load', function(){
   $$('.nav_box_title', '.home_title').each(function(obj){
     obj.observe('mouseover', function(e){
       var img = obj.down();
       img.savedClass = $w(img.className)[1]; // the second class argument must be replaced
       img.removeClassName(img.savedClass);
       if (getContainer(obj).visible()) {
         img.addClassName('home_title_close');
       } else {
         img.addClassName('home_title_open');
       }
     });
     obj.observe('mouseout', function(e){
       var img = obj.down();
       img.removeClassName('home_title_close');
       img.removeClassName('home_title_open');
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
    if (!div.visible())
    {
      img.title = alt_up;
      new Effect.BlindDown(div, {duration:0.6});
      registerHomeFoldStatus(container_id, true);
    }
    else
    {
      img.title = alt_down;
      new Effect.BlindUp(div, {duration:0.6});
      registerHomeFoldStatus(container_id, false);
    }
}

function registerHomeFoldStatus(container_id, opened)
{
  date = new Date;
  date.setFullYear(date.getFullYear()+1);
  document.cookie = container_id + "_home_status=" + escape(opened) + "; expires=" + date.toGMTString();
}