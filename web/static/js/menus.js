startList = function() {
    var m, nav, no, i;
    if (m = document.getElementById("menu_content")) {
        nav = null;
        for (i = 0; m.childNodes.length > i; i++) {
            if (m.childNodes[i].nodeName == 'UL') {
                nav = m.childNodes[i];
                break;
            }
        }
        if (nav)
            for (i = 0; nav.childNodes.length > i; i++) {
                no = nav.childNodes[i];
                if (no.nodeName == 'LI') {
                    no.onmouseover = function() {
                       this.className = "over"; /* NO += " over", per follie IE/Mac */
                    }
                    no.onmouseout = function() {
                       this.className = "" /* NO this.className.replace(" over", "") */
                    }
                }
            }
    }
}
