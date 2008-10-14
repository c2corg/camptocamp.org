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
