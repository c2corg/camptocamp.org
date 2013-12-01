/*
 * Search books on books.google.com
 * TODO add more services (amazon?)
 */
(function(C2C, $) {

  $.extend(C2C.GoogleBooks = C2C.GoogleBooks || {}, {

    show: function(booksInfo) {
      var has_results = false;
      for (var isbn in booksInfo) { //TODO could there be more than one answer?
        has_results = true;
        var book = booksInfo[isbn];
        var info_url = book.info_url;
        var preview_url = book.preview_url;
        var thumbnail_url = book.thumbnail_url;
        var preview = book.preview;
        //var embeddable = book.embeddable;

        // build result html
        var li = $('<li/>');
        li.append('<a href="' + info_url + '" class="external_link">' +
                  C2C.GoogleBooks.translation + '</a><br>');

        if (thumbnail_url) {
          li.append('<a href="' + info_url + '"><img src="' + thumbnail_url + '"></a>');
        }

        if (preview_url && preview != 'noview') {
          li.append('<a href="' + preview_url + '"><img src="' + C2C.GoogleBooks.preview_logo_src + '"></a>');
        } else {
          li.append('<img src="http://books.google.com/googlebooks/images/poweredby.png">');
        }

        $('#buy_books_section_container').append($('<ul/>').append(li));
      }

      if (has_results) {
        // display section
        $('#buy_books_section_title').parents('.article_titre_bg').show();
        $('#buy_books_section_container').show();

        // add anchor link in left navigation menu
        var anchor_title = $('#buy_books_toggle')[0].nextSibling.data;
        $('#nav_anchor_content ul').append(
          $('<li/>').append('<a href="#buy_books" class="picto_books link_nav_anchor" title="' +
                            anchor_title + '">' + anchor_title + '</a>'));
      }
    },

    search: function() {
      var a = document.createElement('script');
      var h = document.getElementsByTagName('head')[0];
      a.async = 1;
      a.src = '//books.google.com/books?bibkeys=' + escape(C2C.GoogleBooks.book_isbn) +
              '&jscmd=viewapi&callback=C2C.GoogleBooks.show';
      h.appendChild(a);
    }
  });
})(window.C2C = window.C2C || {}, jQuery);
