/**
 * History page diff radio buttons behavior.
 * Heavily inspired from Mediawiki code.
 * http://www.mediawiki.org/
 * $Id: diff.js 2271 2007-11-04 10:29:46Z fvanderbiest $
 */
(function() {

    "use strict";

    var doneOnloadHook;

    function hookEvent(hookName, hookFunct) {
        if (window.addEventListener) {
            window.addEventListener(hookName, hookFunct, false);
        } else if (window.attachEvent) {
            window.attachEvent("on" + hookName, hookFunct);
        }   
    }

    function historyRadios(parent) {
        var inputs = parent.getElementsByTagName('input');
        var radios = []; 
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].name == "new" || inputs[i].name == "old") {
                radios[radios.length] = inputs[i];
            }
        }   
        return radios;
    }

    // check selection and tweak visibility/class onclick
    function diffcheck() {
        var ntr = false; // the tr where the "new" radio is checked
        var otr = false; // the tr where the "old" radio is checked
        var hf = document.getElementById('pagehistory');
        if (!hf) {
            return true;
        }   
        var trs = hf.getElementsByTagName('tr');
        for (var i=0;i<trs.length;i++) {
            var inputs = historyRadios(trs[i]);
            if (inputs[1] && inputs[0]) {
                if (inputs[1].checked || inputs[0].checked) { // this row has a checked radio button
                    if (inputs[1].checked && inputs[0].checked && inputs[0].value == inputs[1].value) {
                        return false;
                    }
                    if (otr) { // it's the second checked radio
                        if (inputs[1].checked) {
                            otr.className = "selected";
                            return false;
                        }
                    } else if (inputs[0].checked) {
                        return false;
                    }
                    if (inputs[0].checked) {
                        ntr = trs[i];
                    }
                    if (!otr) {
                        inputs[0].style.visibility = 'hidden';
                    }
                    if (ntr) {
                        inputs[1].style.visibility = 'hidden';
                    }
                    trs[i].className += " selected";
                    otr = trs[i];
                }  else { // no radio is checked in this row
                    if (!otr) {
                        inputs[0].style.visibility = 'hidden';
                    } else {
                        inputs[0].style.visibility = 'visible';
                    }
                    if (ntr) {
                        inputs[1].style.visibility = 'hidden';
                    } else {
                        inputs[1].style.visibility = 'visible';
                    }
                }
            }
        }
        return true;
    }

    // page history stuff
    // attach event handlers to the input elements on history page
    function histrowinit() {
        var hf = document.getElementById('pagehistory');
        if (!hf) {
            return;
        }
        var trs = hf.getElementsByTagName('tr');
        for (var i = 0; i < trs.length; i++) {
            var inputs = historyRadios(trs[i]);
            if (inputs[0] && inputs[1]) {
                inputs[0].onclick = diffcheck;
                inputs[1].onclick = diffcheck;
            }
        }
        diffcheck();
    }

    function runOnloadHook() {
        // don't run anything below this for non-dom browsers
        if (doneOnloadHook || !(document.getElementById && document.getElementsByTagName)) {
            return;
        }

        histrowinit();

        doneOnloadHook = true;
    }

    hookEvent("load", runOnloadHook);
    // minor revisions hidden by default
    // hookEvent("load", toggle_minor_revision);
}());
