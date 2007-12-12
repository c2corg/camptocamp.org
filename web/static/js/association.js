// should empty ajax feedback div
function emptyFeedback(aff)
{
    $(aff).innerHTML="";
    Element.hide($(aff));
}

// should show form, show minus, hide plus
function showForm(form, add, minus) 
{
    Element.show($(form));
    Element.hide($(add));
    Element.show($(minus));
}

// should hide form, hide minus, show plus
function hideForm(form, add, minus)
{
    Element.hide($(form));
    Element.hide($(minus));
    Element.show($(add));
}