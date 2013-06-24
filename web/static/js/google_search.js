(function(C2C) {

  "use strict";

  C2C.GoogleSearch = {

    // FIXME: totalResults seems to vary depending on the startIndex??
    // thus we are not creating a classical pager, only prev and next (if they exist)
    displayPager: function (response) {
      var link, img, url_params;

      var pagesDiv = new Element('div', { 'class': 'pages_navigation' });

      // previous page
      if (response.queries.previousPage) {
        link = new Element('a', { href: 'javascript:C2C.GoogleSearch.search()' });
        img = new Element('span', { 'class': 'picto action_first',
                                  title: this.i18n[0] });
        link.appendChild(img);
        pagesDiv.appendChild(link);

        pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0'));

        url_params = '&start=' + response.queries.previousPage[0].startIndex;
        link = new Element('a', { href: 'javascript:C2C.GoogleSearch.search(\''+url_params+'\')' });
        img = new Element('span', { 'class': 'picto action_back',
                                  title: this.i18n[1] });
        link.appendChild(img);
        pagesDiv.appendChild(link);
      }

      // current results
      if (response.queries.previousPage || response.queries.nextPage) {
        var start = response.queries.request[0].startIndex;
        var end = start + response.queries.request[0].count;
        pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0' + start + '\u00a0-\u00a0' + end + '\u00a0\u00a0'));
      }

      // next page
      if (response.queries.nextPage) {
        url_params = '&start=' + response.queries.nextPage[0].startIndex;
        link = new Element('a', { href: 'javascript:C2C.GoogleSearch.search(\''+url_params+'\')' });
        img = new Element('span', { 'class': 'picto action_next',
                                 title: this.i18n[2] });
        link.appendChild(img);
        pagesDiv.appendChild(link);
      }

      var contentDiv = $('google_search_results');
      contentDiv.appendChild(pagesDiv);
    },

    handleResponse: function(response) {

      if (response.error) {
        if (this.alternate_url) {
          // an error has occured (most probably daily quota exceeded), we redirect to google custom search page
          // (outside c2c, but not submitted to quotas)
          var url = this.alternate_url + '&q=' + this.q;
          window.location = url;
        } else {
          // we don't want to redirect (e.g. because google search was launched automatically)
          // but we hide the error
          $('google_search').hide();
          /*$('google_search_results').update('An error has occured, please <a href="mailto:dev'+'@'
                                            +'camptocamp.org">contact us</a> ('+response.error.message+')');*/
        }
        return;
      }

      if (response.items && response.items.length > 0) {
        var contentDiv = $('google_search_results');
        $(contentDiv).update('');
        var results = response.items;

        var table = new Element('table', { 'class': 'list' });

        var thead = new Element('thead');
        var trh = new Element('tr');
        var th1 = new Element('th');
        var th2 = new Element('th');
        $(th1).update(this.i18n[3]);
        $(th2).update(this.i18n[4]);
        trh.appendChild(th1);
        trh.appendChild(th2);
        thead.appendChild(trh);
        table.appendChild(thead);

        var tbody = new Element('tbody');

        for (var i = 0; i < results.length; i++) {
          var result = results[i];

          var tr = new Element('tr');
          if (i % 2 === 0) {
            tr.setAttribute('class', 'table_list_even');
          } else {
            tr.setAttribute('class', 'table_list_odd');
          }

          var title_str = result.title.split(' ::')[0];
      
          var title = new Element('td');
          title.innerHTML = '<a href="' + result.link + '">' + title_str + '</a>';
          var content = new Element('td');
          content.innerHTML = result.htmlSnippet;

          tr.appendChild(title);
          tr.appendChild(content);
          tbody.appendChild(tr);
        }
        table.appendChild(tbody);
        contentDiv.appendChild(table);

        this.displayPager(response);
      } else {
        $('google_search_results').update(this.i18n[5]);
      }
    },

    search: function(params) {
      // load script asynchronously
      // once loaded, it will call handleResponse()
      var url = this.base_url + '&q=' + this.q;
      if (params) url += params;
      var head = $$('head')[0];
      var script = new Element('script', { type: 'text/javascript',
                                           async: true,
                                           src:   url });
      head.appendChild(script);  
    }
  };

})(window.C2C = window.C2C || {});
