var startList = function() {
    var nav, no, i;
    var m = document.getElementById("menu_content");
    if (m) {
        nav = null;
        for (i = 0; m.childNodes.length > i; i++) {
            if (m.childNodes[i].nodeName == 'UL') {
                nav = m.childNodes[i];
                break;
            }
        }
        if (nav) {
            var f1 = function() {
                this.className = "over"; /* NO += " over", per follie IE/Mac */
            };
            var f2 = function() {
                this.className = ""; /* NO this.className.replace(" over", "") */
            };
            for (i = 0; nav.childNodes.length > i; i++) {
                no = nav.childNodes[i];
                if (no.nodeName == 'LI') {
                    no.onmouseover = f1;
                    no.onmouseout = f2;
                }
            }
        }
    }
};
