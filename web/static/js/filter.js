var activities = new Array();

function update_on_select_change(field, optionIndex)
{
    index = $(field + '_sel').value;
    if (index == '0' || index >= 4)
    {
        $(field + '_span1').hide();
        $(field + '_span2').hide();
        if (optionIndex >= 4)
        {
            if (index == 4)
            {
                $(field + '_span3').show();
            }
            else
            {
                $(field + '_span3').hide();
            }
        }
    }
    else
    {
        $(field + '_span1').show();
        if (index == 3)
        {
            $(field + '_span2').show();
        }
        else
        {
            $(field + '_span2').hide();
        }
        if (optionIndex >= 4)
        {
            $(field + '_span3').hide();
        }
    }
}

function initialize_select()
{
    var field_list = new Array();
    var re = new RegExp('_sel$', 'i');
    var sel_list = document.getElementsByTagName('SELECT');
    for (var i = 0; i < sel_list.length; ++i)
    {
        if (sel_list[i].id.search(re) != -1)
        {
            sel_list[i].onchange(true);
        }
    }
}

// see hide_unrelated_fields() in routes.js
function hide_unrelated_filter_fields(current_activity)
{
    if (activities.indexOf(current_activity) != -1)
    {
         // if activity is already selected, unselect it
         activities = activities.without(current_activity);
    }
    else
    {
         // else add it to the selection
         activities.push(current_activity);
    }

    show_flags = new Array
    (
        'ski',
        'ski_snow_mountain',
        'ski_snow_mountain_rock',
        'ski_snow_mountain_rock_ice',
        'snow_ice',
        'snow_mountain_rock_ice',
        'rock_mountain',
        'hiking'
    );
    
    show_flags.each(function(flag)
    {
        eval('show_' + flag + ' = false');
    });
    show_snow = false;

    activities.each(function(activity)
    {
        switch (activity)
        {
            case 1: // skitouring
                show_ski = true;
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                break;

            case 2: // snow_ice_mixed
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case 3: // mountain_climbing
                show_ski_snow_mountain = true;
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case 4: // rock_climbing
                show_ski_snow_mountain_rock = true;
                show_ski_snow_mountain_rock_ice = true;
                show_snow_mountain_rock_ice = true;
                show_rock_mountain = true;
                break;

            case 5: // ice_climbing
                show_ski_snow_mountain_rock_ice = true;
                show_snow_ice = true;
                show_snow_mountain_rock_ice = true;
                break;

            case 6: // hiking
                show_hiking = true;
        }
    });

    show_flags.each(function(flag)
    {
        div_id = flag + '_fields';
        if (eval('show_' + flag))
        {
            $(div_id).show();
        }
        else
        {
            $(div_id).hide();
        }
    });
    
    if (document.getElementById('conf') && show_ski_snow_mountain_rock)
    {
        select_size = 6;
        if (show_snow)
        {
            $('conf').options[4].show();
            $('conf').options[5].show();
        }
        else
        {
            $('conf').options[4].hide();
            $('conf').options[5].hide();
            select_size -= 2;
        }
        
        $('conf').size = select_size;
    }
}

function initialize_activities()
{
    var act_form = document.getElementById('actform');
    var act_list = new Array();
    if(act_form)
    {
        act_list = act_form.getElementsByTagName("INPUT");
        for (var i = 0; i < act_list.length; ++i)
        {
            if(act_list[i].checked)
            {
                act_list[i].onclick(true);
            }
        };
    }
}

Event.observe(window, 'load', function()
{
    initialize_activities();
    initialize_select();
    
    if(typeof(focus_field) != 'undefined')
    {
        $(focus_field).focus();
    }
});

function changeSelectSize(select_id, up_down)
{
    height = $(select_id).offsetHeight;
    if(up_down)
    {
        height += 150; 
    }
    else
    {
        height = Math.max(100, height - 150);
    }
    $(select_id).style.height = height + "px";
}

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
    img.setAttribute('title', google_i18n[0]);
    img.setAttribute('src', '/static/images/picto/first.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);

    pagesDiv.appendChild(document.createTextNode('\u00a0'));

    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage('+(curPage-1)+');';
    img = document.createElement('img');
    img.setAttribute('alt', '<');
    img.setAttribute('title', google_i18n[1]);
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
      $(label).update(page.label);
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
    img.setAttribute('title', google_i18n[2]);
    img.setAttribute('src', '/static/images/picto/next.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);

    pagesDiv.appendChild(document.createTextNode('\u00a0'));

    link = document.createElement('a');
    link.href = 'javascript:siteSearch.gotoPage('+(cursor.pages.length-1)+');';
    img = document.createElement('img');
    img.setAttribute('alt', '>>');
    img.setAttribute('title', google_i18n[3]);
    img.setAttribute('src', '/static/images/picto/last.png');
    link.appendChild(img);
    pagesDiv.appendChild(link);
  } else {
    pagesDiv.appendChild(document.createTextNode('\u00a0\u00a0'));
    link = document.createElement('a');
    link.setAttribute('href', cursor.moreResultsUrl);
    link.innerHTML = google_i18n[4];
    pagesDiv.appendChild(link);
  }

  var contentDiv = $('google_search_results');
  contentDiv.appendChild(pagesDiv);
}

function google_search_complete() {
  var regexp = /\b\s::\s(.*)$/;

  if (siteSearch.results && siteSearch.results.length > 0) {
    var contentDiv = $('google_search_results');
    $(contentDiv).update('');
    var results = siteSearch.results;
    
    var table = document.createElement('table');
    table.setAttribute('class', 'list');

    var thead = document.createElement('thead');
    var trh = document.createElement('tr');
    var th1 = document.createElement('th');
    var th2 = document.createElement('th');
    $(th1).update(google_i18n[5]);
    $(th2).update(google_i18n[6]);
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
    $('google_search_results').update(google_i18n[7]);
  }

}

function init_google_search() {
  google.search.Search.getBranding($("google_search_branding"));

  siteSearch = new google.search.WebSearch();
  siteSearch.setResultSetSize(google.search.Search.LARGE_RESULTSET);
  siteSearch.setUserDefinedClassSuffix("siteSearch");
  siteSearch.setSiteRestriction(module_url);
  siteSearch.setSearchCompleteCallback(this, google_search_complete, null);
}

google.setOnLoadCallback(init_google_search, true);

