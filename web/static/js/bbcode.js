var bbisMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
var bbregexp = new RegExp("[\r]","gi");
var opening_tag, closing_tag;

function storeCaret(selec, targetElm)
{
  var oField;
  if (selec == "wl") // wikilink
  {
    opening_tag = "[[|";
    closing_tag = "]]";
    selec = "";
  }
  else
  {
    if (selec == "L#") // route line
    {
      opening_tag = "L# | ";
      closing_tag = " |  |  | ";
      selec = "";
    }
    else
    {
      opening_tag = "[" + selec + "]";
      closing_tag = "[/" + selec + "]";
    }
  }

  if (bbisMozilla) 
  {
  // Mozilla
    //oField = document.forms['news'].elements['newst'];
    oField = $(targetElm);

    var objectValue = oField.value;

    var scrollPos = oField.scrollTop;
    var startPos = oField.selectionStart;
    var endPos = oField.selectionEnd;

    var objectValueDeb = objectValue.substring(0, startPos);
    var objectValueFin = objectValue.substring(endPos , oField.textLength );
    var objectSelected = objectValue.substring(startPos, endPos);
      
    var objectValueDebN = objectValueDeb + opening_tag + objectSelected + closing_tag;
    oField.value = objectValueDebN + objectValueFin;
    oField.selectionStart = objectValueDeb.length;
    oField.selectionEnd = objectValueDebN.length;
    oField.scrollTop = scrollPos;
    oField.focus();
    var newPos;
    if (opening_tag == "[[|" || objectSelected.length === 0)
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
      for(var n = 0; n < i; n++)
      {if(bbregexp.test(oField.value.substr(n,2))){r++;}}
      var pos = i + 2 + selec.length - r;
      r = oField.createTextRange();
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
    var height = $(textarea_id).offsetHeight;
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

function doUpdateLegend()
{
    var custom = $('customlegend').checked;
    var legend = $('legend');
    legend.disabled = !custom;
    if (!custom)
    {
        var txt = $$('.selected_image')[0].down().down().alt;
        legend.value = txt;
    }
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

function doInsertImgTag()
{
    var align = '';
    $$('input[name=alignment]').each(function(obj){
        if (obj.checked)
        {
            align = obj.value;
        }
    });
    var borderlegend = '';
    if ($('hideborderlegend').checked)
    {
        borderlegend = ' no_border no_legend';
    }
    var txt = '[img=' + $('id').value + ' ' + align + borderlegend;
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
    var oField = $($('div').value);
    if (bbisMozilla) 
    {
    // Mozilla
      var objectValue = oField.value;

      var scrollPos = oField.scrollTop;
      var startPos = oField.selectionStart;
      var endPos = oField.selectionEnd;

      var objectValueDeb = objectValue.substring(0, startPos);
      var objectValueFin = objectValue.substring(endPos , oField.textLength );

      var objectValueDebN = objectValueDeb + txt;
      oField.value = objectValueDebN + objectValueFin;
      oField.selectionStart = objectValueDeb.length;
      oField.scrollTop = scrollPos;
      oField.focus();
      var newPos = oField.selectionStart;
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
        for(var n = 0; n < i; n++)
        {if(bbregexp.test(oField.value.substr(n,2))){r++;}}
        var pos = i + 2 - r;
        r = oField.createTextRange();
        r.moveStart('character', pos);
        r.collapse();
        r.select();
      }
    }
    oField.focus();
    Modalbox.hide();
}
