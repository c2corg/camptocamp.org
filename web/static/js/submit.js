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

function showFieldDefault(field, enable)
{
    if(field_default[field][2])
    {
        var field_id = field_default[field][0];
        if(enable)
        {
            $(field_id).value = field_default[field][1];
            $(field_id).style.color = 'gray';
        }
        else
        {
            $(field_id).value = '';
            $(field_id).style.color = 'black';
        }
    }
}

function showAllFieldDefault(enable)
{
    if(typeof(field_default)!='undefined')
    {
        for (i=0; i < field_default.length; i++)
        {
            showFieldDefault(i, enable);
        }
    }
}

function initFieldDefault()
{
    if(typeof(field_default)!='undefined' && typeof(ga_done)!='undefined')
    {
        var field_id;
        for (i=0; i < field_default.length; i++)
        {
            field_id = field_default[i][0];
            if($(field_id).value == '')
            {
                field_default[i][2] = true;
                showFieldDefault(i, true);
            }
            else
            {
                field_default[i][2] = false;
            }
        }
    }
}

function hideFieldDefault(field)
{
    if(field_default[field][2])
    {
        showFieldDefault(field, false);
        field_default[field][2] = false;
    }
}

function hideAllFieldDefault()
{
    if(typeof(field_default)!='undefined')
    {
        for (i=0; i < field_default.length; i++)
        {
            hideFieldDefault(i);
        }
    }
}

Event.observe(window, 'load', initFieldDefault);

Event.observe(window, 'load', function() {
    Event.observe('editform', 'submit', hideAllFieldDefault);
});

