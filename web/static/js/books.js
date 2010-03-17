/*
 * Search books on books.google.com
 */

GoogleBooks = {

  show: function(booksInfo) {
    var has_results = false;
    for (isbn in booksInfo) {       //TODO could there be more than one answer?
      has_results = true;
      var book = booksInfo[isbn];
      var info_url = book.info_url;
      var preview_url = book.preview_url;
      var thumbnail_url = book.thumbnail_url;
      var preview = book.preview;
      var embeddable = book.embeddable;

      var ul = new Element('ul', { className: 'children_docs' });

      var li = new Element('li');
      var link = new Element('a', { href: info_url, className: 'external_link' });
      link.appendChild(document.createTextNode(google_books_translation));
      li.appendChild(link);

      li.appendChild(new Element('br'));

      if (thumbnail_url) {
        link = new Element('a', { href: info_url });
        var img = new Element('img', { src: thumbnail_url });
        link.appendChild(img);
        li.appendChild(link);
      }

      if (preview_url && preview != 'noview') {
        var preview_link = new Element('link', { href: preview_url });
        var preview_logo = new Element('img', { src: preview_logo_src });
        preview_link.appendChild(preview_logo);
        li.appendChild(preview_link);
      } else { // need to display branding if no preview (CGU)
        var branding_img = new Element('img', { src: 'http://books.google.com/googlebooks/images/poweredby.png' });
        li.appendChild(branding_img);
      }

      ul.appendChild(li);
      new Insertion.Bottom('buy_books_section_container', ul)
    }

    if (has_results) {
      $('buy_books_section_title').up('.article_titre_bg').show();
      $('buy_books_section_container').show();
    }
  },

  search: function() {
    var scriptElement = new Element('script', {
      src: 'http://books.google.com/books?bibkeys='+escape(book_isbn)+'&jscmd=viewapi&callback=GoogleBooks.show',
      type: 'text/javascript'
    });
    document.documentElement.firstChild.appendChild(scriptElement);
  }

};

if (typeof(book_isbn) !== 'undefined') {
  Event.observe(window, 'load', GoogleBooks.search);
}

// TODO use other services (amazon?)
