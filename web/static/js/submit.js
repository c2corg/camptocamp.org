function submitonce(aform) {
    switchFormButtonsStatus(aform, true);
}

function switchFormButtonsStatus(aform, disable) {
    if (document.all || document.getElementById){
        var tmp_dom_obj;
        for (i=0; i < aform.length; i++){        
            tmp_dom_obj = aform.elements[i];
            if ((tmp_dom_obj.type.toLowerCase() == "submit") || (tmp_dom_obj.type.toLowerCase() == "button"))
                tmp_dom_obj.disabled = disable
        }
    }
}

function getWizardRouteRatings() {
    new Ajax.Updater('route_descr', '/routes/getratings',
                     {asynchronous:true, evalScripts:false,
                      onComplete:function(request, json){Element.hide("indicator")},
                      onFailure:function(request, json){Element.hide("wizard_route_descr")},
                      onLoading:function(request, json){Element.show("indicator")},
                      onSuccess:function(request, json){Element.show("wizard_route_descr")},
                      parameters:"id=" + $("routes").value});
}

function digit(event) {
// Compatibility IE / Firefox
if(!event&&window.event) {
event=window.event;
}
// IE
if((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 46 && event.keyCode != 8) {
event.returnValue = false;
event.cancelBubble = true;
}
// DOM
if((event.which < 48 || event.which > 57) && event.which != 37 && event.which != 39 && event.which != 46 && event.which != 8 && event.which != "keyleft") {
//event.preventDefault();
//event.stopPropagation();
}
}
