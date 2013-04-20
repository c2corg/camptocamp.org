// this is ie6 only and is loaded just after the menu has been defined in DOM
(function() {

  var nav, no, i;

  // retrieve the ul element right after the menu_content div
  var m = document.getElementById("menu_content");
  if (m) {
    nav = null;
    for (i = 0; m.childNodes.length > i; i++) {
      if (m.childNodes[i].nodeName == 'UL') {
        nav = m.childNodes[i];
        break;
      }
    }
  }

  if (nav) {

    // each li child nodes of ul get the over class when hovered
    // hack for ie6 not supporting :hover on non <a> tags
    for (i = 0; nav.childNodes.length > i; i++) {
      no = nav.childNodes[i];
      if (no.nodeName == 'LI') {
        no.onmouseover = function() {
          this.className = "over";
        };
        no.onmouseout = function() {
          this.className = "";
        };
      }
    }
        
    // hide select tags when displaying the menu
    var selectList = document.getElementsByTagName('select');

    nav.onmouseover = function() {
      if (selectList) {
        var len=selectList.length;
        if (len>0) {
          for (i=0;i<len;i++) {
            selectList[i].style.display='none';
          }
        }
      }
    };

    nav.onmouseout = function() {
      if(selectList) {
        var len=selectList.length;
        if (len>0) {
          for (i=0;i<len;i++) {
            selectList[i].style.display='';
          }
        }
      }
    };

  }

})();
