var search = null;
var div_id_search_results = null;

/*
 *
 * Public functions
 *
 *   used from within HTML
 *
 */


function activate_search(_form_id_search, _div_id_search_results, _search_url) {
    // create search object
    search = new CartoWeb.SearchForm(
        _form_id_search,
        _search_url,
        __ajax_search_pre_callback,
        __ajax_search_post_callback,
        null
    );
    div_id_search_results = _div_id_search_results;
}


function do_search(form) {
    if (search) {
        // scroll to the results section first
        var div = __map_is_visible() ? div_id_map_container : div_id_search_results;
        new Effect.ScrollTo(div, {offset: -35});
        // trigger search
        search.search();
    }
}

/*
 *
 * Private functions
 *
 *   used from this file and from mapping.js
 *
 */

function __ajax_search_pre_callback(request) {
    $(div_id_search_results).innerHTML = '<img src="/static/images/indicator.gif"></img>';
}

function __ajax_search_post_callback(request) {
    var json = eval('(' + request.responseText + ')');
    if (!div_id_search_results) {
        return;
    } 
    $(div_id_search_results).innerHTML = json["html"];
    __create_markers(json["geo"]);
}

