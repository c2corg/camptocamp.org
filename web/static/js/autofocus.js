// autofocus polyfill

(function () {
  // Proceed only if new inputs don't have the autofocus property
  if ( document.createElement("input").autofocus === undefined ) {
    // Get a reference to all forms, and an index variable
    var forms = document.forms, fIndex = -1;
    // Begin cycling over all forms in the document
    formloop: while ( ++fIndex < forms.length ) {
      // Reference all elements in form, and an index variable
      var elements = forms[ fIndex ].elements, eIndex = -1;
      // Begin cycling over all elements in collection
      while ( ++eIndex < elements.length ) {
        // Check for the autofocus attribute
        if ( elements[ eIndex ].attributes["autofocus"] ) {
          // If found, trigger focus
          elements[ eIndex ].focus();
          // And break out of outer loop
          break formloop;
        }
      }
    }
  }
}());
