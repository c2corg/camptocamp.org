(function(C2C, $) {

  $.extend(C2C.GoogleSearch = C2C.GoogleSearch || {}, {

    // FIXME: totalResults seems to vary depending on the startIndex??
    // thus we are not creating a classical pager, only prev and next (if they exist)
    displayPager: function(response) {
      var pagesDiv = $('<div class="pages_navigation"/>');

      // previous page
      if (response.queries.previousPage) {
        pagesDiv.append('<a href="#" onclick="C2C.GoogleSearch.search()"><span class="picto action_first" title="' +
                        C2C.GoogleSearch.i18n[0] + '"></span></a>\u00a0\u00a0' +
                        '<a href="#" onclick="C2C.GoogleSearch.search(\'&start=' + response.queries.previousPage[0].startIndex +
                        '\')"><span class="picto action_back" title="' + C2C.GoogleSearch.i18n[1] + '"></span></a>');
      }

      // current results
      if (response.queries.previousPage || response.queries.nextPage) {
        var start = response.queries.request[0].startIndex;
        var end = start + response.queries.request[0].count;
        pagesDiv.append('\u00a0\u00a0' + start + '\u00a0-\u00a0' + end + '\u00a0\u00a0');
      }

      // next page
      if (response.queries.nextPage) {
        pagesDiv.append('<a href="#" onclick="C2C.GoogleSearch.search(\'&start=' + response.queries.nextPage[0].startIndex +
                        '\')"><span class="picto action_next" title="' + C2C.GoogleSearch.i18n[2] + '"></span></a>');
      }

      $('#google_search_results').append(pagesDiv);
    },

    handleResponse: function(response) {

      if (response.error) {
        if (C2C.GoogleSearch.alternate_url) {
          // an error has occured (most probably daily quota exceeded), we redirect to google custom search page
          // (outside c2c, but not submitted to quotas)
          var url = C2C.GoogleSearch.alternate_url + '&q=' + C2C.GoogleSearch.q;
          window.location = url;
        } else {
          // we don't want to redirect (e.g. because google search was launched automatically)
          // but we hide the error
          $('#google_search').hide();
        }
        return;
      }

      if (response.items && response.items.length > 0) {
        var results = response.items;

        var thead = $('<thead/>')
          .append($('<tr/>')
            .append($('<th/>').text(C2C.GoogleSearch.i18n[3]),
                    $('<th/>').text(C2C.GoogleSearch.i18n[4])));

        var tbody = $('<tbody/>');
        for (var i = 0, len = results.length; i < len; i++) {
          var title_str = results[i].title.split(' ::')[0];
          tbody
            .append($('<tr/>').addClass('table_list_' + (i%2 ? 'even' : 'odd'))
              .append('<td><a href="' + results[i].link + '">' + title_str + '</a></td>' +
                      '<td>' + results[i].htmlSnippet + '</td>'));
        }

        $('#google_search_results')
          .html($('<table/>')
            .append(thead, tbody));

        C2C.GoogleSearch.displayPager(response);

      } else {
        $('#google_search_results').text(C2C.GoogleSearch.i18n[5]);
      }
    },

    search: function(params) {
      // load script asynchronously
      // once loaded, it will call handleResponse()
      var url = C2C.GoogleSearch.base_url + '&q=' + C2C.GoogleSearch.q;
      if (params) url += params;

      // note: we don't use $.getScript, because it's eval-ing it
      var a = document.createElement('script');
      var h = document.getElementsByTagName('head')[0];
      a.async = 1;
      a.src = url;
      h.appendChild(a);
    }
  });

})(window.C2C = window.C2C || {}, jQuery);
