function submitonce(aform){
    if (document.all || document.getElementById){
        var tmp_dom_obj;
        for (i=0; i < aform.length; i++){        
            tmp_dom_obj = aform.elements[i];
            if ((tmp_dom_obj.type.toLowerCase() == "submit") || (tmp_dom_obj.type.toLowerCase() == "button"))
                tmp_dom_obj.disabled=true
        }
    }
}