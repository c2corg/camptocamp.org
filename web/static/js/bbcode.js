var bbisMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
var bbregexp = new RegExp("[\r]","gi");
var opening_tag, closing_tag;

function storeCaret(selec, targetElm)
{
  if (selec == "wl") // wikilink
  {
    opening_tag = "[[|";
    closing_tag = "]]";
    selec = "";
  }
  else
  {
    opening_tag = "[" + selec + "]";
    closing_tag = "[/" + selec + "]";  
  }

  if (bbisMozilla) 
  {
  // Mozilla
    //oField = document.forms['news'].elements['newst'];
    oField = $(targetElm);

    objectValue = oField.value;

    var scrollPos = oField.scrollTop;
    startPos = oField.selectionStart;
    endPos = oField.selectionEnd;

    objectValueDeb = objectValue.substring(0, startPos);
    objectValueFin = objectValue.substring(endPos , oField.textLength );
    objectSelected = objectValue.substring(startPos, endPos);
      
    objectValueDebN = objectValueDeb + opening_tag + objectSelected + closing_tag;
    oField.value = objectValueDebN + objectValueFin;
    oField.selectionStart = objectValueDeb.length;
    oField.selectionEnd = objectValueDebN.length;
    oField.scrollTop = scrollPos;
    oField.focus();
    if (opening_tag == "[[|" || objectSelected.length == 0)
    {
      newPos = objectValueDeb.length + selec.length + 2;
    }
    else
    {
      newPos = oField.selectionEnd;
    }
    oField.setSelectionRange(newPos, newPos);
  }
  else
  {
  // IE
    oField = $(targetElm);
    var str = document.selection.createRange().text;

    if (str.length>0)
    {
      // some text has been selected
      var sel = document.selection.createRange();
      sel.text = opening_tag + str + closing_tag;
      sel.collapse();
      sel.select();
    }
    else
    {
  	  oField.focus(oField.caretPos);
      oField.focus(oField.value.length);
      oField.caretPos = document.selection.createRange().duplicate();
      
      var bidon = "%~%";
      var orig = oField.value;
      oField.caretPos.text = bidon;
      var i = oField.value.search(bidon);
      oField.value = orig.substr(0,i) + opening_tag + closing_tag + orig.substr(i, oField.value.length);
      var r = 0;
      for(n = 0; n < i; n++)
      {if(bbregexp.test(oField.value.substr(n,2)) == true){r++;}};
      pos = i + 2 + selec.length - r;
      var r = oField.createTextRange();
      r.moveStart('character', pos);
      r.collapse();
      r.select();
    }
  }
  oField.focus();
  return;
}

function changeTextareaSize(textarea_id, up_down)
{
    height = $(textarea_id).offsetHeight;
    if(up_down)
    {
        height += 80; 
    }
    else
    {
        height = Math.max(60, height - 80);
    }
    $(textarea_id).style.height = height + "px";
}

// img tag below
function updateSelectedImage(img)
{
    // update selected image
    $$('div.image').each(function(obj){
        obj.removeClassName('selected_image');
    });
    var selected =  img.up().up();
    selected.addClassName('selected_image');
    // update legend
    doUpdateLegend();
    // update form id
    $('id').value = selected.id.substring(17);
}

function doUpdateLegend()
{
    var custom = $('customlegend').checked;
    var legend = $('legend');
    legend.disabled = !custom;
    if (custom == false)
    {
        var txt = $$('.selected_image')[0].down().down().alt;
        legend.value = txt;
    }
}

function doInsertImgTag()
{
    var align = '';
    $$('input[name=alignment]').each(function(obj){
        if (obj.checked)
        {
            align = obj.value;
        }
    });
    var txt = '[img=' + $('id').value + ' ' + align;
    if ($('customlegend').checked)
    {
        txt += ']' + $('legend').value + '[/img]';
    }
    else
    {
        txt += '/]';
    }
    txt += "\n";

    // paste image tag to text area
    oField = $($('div').value);
    if (bbisMozilla) 
    {
    // Mozilla
      objectValue = oField.value;

      var scrollPos = oField.scrollTop;
      startPos = oField.selectionStart;
      endPos = oField.selectionEnd;

      objectValueDeb = objectValue.substring(0, startPos);
      objectValueFin = objectValue.substring(endPos , oField.textLength );
      objectSelected = objectValue.substring(startPos, endPos);

      objectValueDebN = objectValueDeb + txt;
      oField.value = objectValueDebN + objectValueFin;
      oField.selectionStart = objectValueDeb.length;
      oField.scrollTop = scrollPos;
      oField.focus();
      newPos = oField.selectionStart;
      oField.setSelectionRange(newPos, newPos);
    }
    else
    {
    // IE
      var str = document.selection.createRange().text;

      if (str.length>0)
      {
        // some text has been selected
        var sel = document.selection.createRange();
        sel.text = txt;
        sel.collapse();
        sel.select();
      }
      else
      {
        oField.focus(oField.caretPos);
        oField.focus(oField.value.length);
        oField.caretPos = document.selection.createRange().duplicate();

        var bidon = "%~%";
        var orig = oField.value;
        oField.caretPos.text = bidon;
        var i = oField.value.search(bidon);
        oField.value = orig.substr(0,i) + txt + orig.substr(i, oField.value.length);
        var r = 0;
        for(n = 0; n < i; n++)
        {if(bbregexp.test(oField.value.substr(n,2)) == true){r++;}};
        pos = i + 2 + selec.length - r;
        var r = oField.createTextRange();
        r.moveStart('character', pos);
        r.collapse();
        r.select();
      }
    }
    oField.focus();
    Modalbox.hide();
}
