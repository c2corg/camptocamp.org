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

    var pos = oField.scrollTop;
    deb = oField.selectionStart;
    fin = oField.selectionEnd;

    objectValueDeb = objectValue.substring( 0 , oField.selectionStart );
    objectValueFin = objectValue.substring( oField.selectionEnd , oField.textLength );
    objectSelected = objectValue.substring( oField.selectionStart , oField.selectionEnd );
      
    oField.value = objectValueDeb + opening_tag + objectSelected + closing_tag + objectValueFin;
    oField.selectionStart = objectValueDeb.length;
    objectValueDebN = objectValueDeb + opening_tag + objectSelected + closing_tag;
    oField.selectionEnd = objectValueDebN.length;
    oField.scrollTop = pos;
    oField.focus();
    oField.setSelectionRange(
      objectValueDeb.length + selec.length + 2,
      objectValueDeb.length + selec.length + 2);
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
}