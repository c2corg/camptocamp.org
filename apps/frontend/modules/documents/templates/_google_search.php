<?php if(in_array($module, array('areas', 'articles', 'books', 'huts', 'outings', 'parkings', 'routes', 'sites', 'summits'))): ?>
<br />
<br />
<hr />
<br />

<script src="http://www.google.com/jsapi" type="text/javascript"></script> <!-- TODO put this somewhere else? -->
<script language="Javascript" type="text/javascript">
//<![CDATA[
google.load('search', '1');

var siteSearch;

function google_search_pager() {
  var cursor = siteSearch.cursor;
  var curPage = cursor.currentPageIndex;
  var pagesDiv = document.createElement('div');
  pagesDiv.setAttribute('class', 'pages_navigation');

  var link;
  var img;
  var start;

  if (cursor.pages.length < 2) {
    return;
  }

  if (curPage >= 1) {
    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage(0);';
    img = document.createElement('img');
    img.setAttribute('alt', '<<');
    img.setAttribute('title', '<?php echo __('first page') ?>');
    img.setAttribute('src', '/static/images/picto/first.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);

    pagesDiv.appendChild(document.createTextNode('\u00a0'));

    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage('+(curPage-1)+');';
    img = document.createElement('img');
    img.setAttribute('alt', '<');
    img.setAttribute('title', '<?php echo __('previous page') ?>');
    img.setAttribute('src', '/static/images/picto/back.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);
  }

  pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0'));

  if (curPage < 2) { start = 0; }
  else if (curPage > cursor.pages.length - 3) { start = cursor.pages.length - 5; }
  else { start = curPage - 2; }

  for (var i = start; i < Math.min(start+5, cursor.pages.length); i++) {
    var page = cursor.pages[i];
    if (curPage == i) {
      var label = document.createElement('span');
      label.update(page.label);
      pagesDiv.appendChild(label);
    } else {
      var link = document.createElement('a');
      link.href = 'javascript:siteSearch.gotoPage('+i+');';
      link.innerHTML = page.label;
      pagesDiv.appendChild(link);
    }
    pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0'));
  }

  if (curPage < cursor.pages.length - 1) {
    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage('+(curPage+1)+');';
    img = document.createElement('img');
    img.setAttribute('alt', '>');
    img.setAttribute('title', '<?php echo __('next page') ?>');
    img.setAttribute('src', '/static/images/picto/next.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);

    pagesDiv.appendChild(document.createTextNode('\u00a0'));

    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage('+(cursor.pages.length-1)+');';
    img = document.createElement('img');
    img.setAttribute('alt', '>>');
    img.setAttribute('title', '<?php echo __('last page') ?>');
    img.setAttribute('src', '/static/images/picto/last.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);
  } else {
    pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0'));
    link = document.createElement('a');
    link.setAttribute('href', cursor.moreResultsUrl);
    link.innerHTML = '<?php echo __('More results on Google...') ?>';
    pagesDiv.appendChild(link);
  }

  var contentDiv = $('google_search_results');
  contentDiv.appendChild(pagesDiv);
}

function google_search_complete() {
  var regexp = /\b\s::\s(.*)$/;

  if (siteSearch.results && siteSearch.results.length > 0) {
    var contentDiv = $('google_search_results');
    contentDiv.update('');
    var results = siteSearch.results;
    
    var table = document.createElement('table');
    table.setAttribute('class', 'list');

    var thead = document.createElement('thead');
    var trh = document.createElement('tr');
    var th1 = document.createElement('th');
    var th2 = document.createElement('th');
    th1.update('<?php echo __('Document title') ?>');
    th2.update('<?php echo __('Extract') ?>');
    trh.appendChild(th1);
    trh.appendChild(th2);
    thead.appendChild(trh);
    table.appendChild(thead);

    var tbody = document.createElement('tbody');

    for (var i = 0; i < results.length; i++) {
      var result = results[i];

      var tr = document.createElement('tr');
      if (i % 2 == 0) {
        tr.setAttribute('class', 'table_list_even');
      } else {
        tr.setAttribute('class', 'table_list_odd');
      }

      if (regexp.test(result.titleNoFormatting)) {
        title_str = regexp.exec(result.titleNoFormatting);
      } else {
        title_str[0] = result.titleNoFormatting;
      }

      var title = document.createElement('td');
      title.innerHTML = '<a href="' + result.unescapedUrl + '">' + title_str[1] + '</a>';
      var content = document.createElement('td');
      content.innerHTML = result.content;

      tr.appendChild(title);
      tr.appendChild(content);
      tbody.appendChild(tr);
    }
    table.appendChild(tbody);
    contentDiv.appendChild(table);

    google_search_pager();

  } else {
    $('google_search_results').update('<?php echo __('No result') ?>');
  }

}

function init_google_search() {
  google.search.Search.getBranding($("google_search_branding"));

  siteSearch = new google.search.WebSearch();
  siteSearch.setResultSetSize(google.search.Search.LARGE_RESULTSET);
  siteSearch.setUserDefinedClassSuffix("siteSearch");
  siteSearch.setSiteRestriction("www.camptocamp.org/<?php echo $module ?>/"); // TODO maybe c2c.org shouldn't be hardcoded 
  siteSearch.setSearchCompleteCallback(this, google_search_complete, null);
}

google.setOnLoadCallback(init_google_search, true);
//]]>
</script>
<div id="google_search">
<?php
echo __('Search with google');
echo form_tag('', array('onsubmit' => 'siteSearch.execute($F(google_search_input)); return false;'));
?>
<span id="google_search_branding" style="float:left"></span>
<?php echo input_tag('google_search_input'); ?>

<?php echo submit_tag(__('Search'), array('name'=>'google_search_submit')); ?>
</form>
<div id="google_search_results"></div>
</div>
<?php endif; ?>
