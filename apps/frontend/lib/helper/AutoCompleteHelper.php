<?php
/**
 * Autocomplete tools
 * @version $Id:$
 */

// FIXME : dirty trick
if (isset($sf_user))
{
    // we are in a template 
    use_helper('JavascriptQueue','Tag','Url','I18N','Asset', 'Viewer', 'MyForm', 'Form', 'General', 'MyMinify');
}
else
{
    // we are in an action
    sfLoader::loadHelpers(array('Tag','Url','I18N','Asset', 'Viewer', 'MyForm', 'Form', 'JavascriptQueue', 'General', 'MyMinify'));
}

function c2c_input_auto_complete($module, $update_hidden, $options)
{
    $field_prefix = _option($options, 'field_prefix', '');
    $display = _option($options, 'display', '');
    $size = _option($options, 'size', '45');
    $extra_params = _option($options, 'extra_params');

    $id = $field_prefix.'_'.$module.'_name';
    $tag = input_tag($module . '_name', $display, array(
               'size' => $size, 'id' => $id,
               'placeholder' => ($module == 'users') ? __('ID or name/nickname') :  __('Keyword or ID')));

    $js = javascript_queue("$('#$id').c2cAutocomplete({
      url: '" . url_for("$module/autocomplete") . "',
      minChars: " . sfConfig::get('app_autocomplete_min_chars') .
      (isset($extra_params) ? ", params: '$extra_params'" : '') . "
    }).on('itemselect', function(e, item) {
      $('#$update_hidden').val(item.id);
    });");

    return $tag . $js;
}

function c2c_auto_complete($module, $update_hidden, $options)
{
    $field_prefix = isset($options['field_prefix']) ? $options['field_prefix'] : '';
    $display_button = _option($options, 'display_button', true);

    // updated field name can be customized so that there is no interference
    // between different autocomplete forms by using field_prefix
    $field = $field_prefix . '_' . $module . '_name';

    $out = c2c_input_auto_complete($module, $update_hidden, $options);
    $out .= ($display_button) ? c2c_submit_tag(__('Link'), array(
                                    'class' => 'samesize',
                                    'onclick' => "$('#$field').val('');",
                                    'picto' =>  'action_create')) : '';
    return $out;
}

/*
 * service = nominatim or geonames
 */
function geocode_auto_complete($name, $service)
{
    $mobile_version = c2cTools::mobileVersion();

    $service_class = ($service === 'nominatim') ? ' nominatim' : ' geonames';

    $out = input_tag($name, '', array('class' => 'geocode_auto_complete' . $service_class,
                                      'placeholder' => __('enter place name'),
                                      'data-noresult' => __('no results')));
    if ($mobile_version)
    {
        $out .= content_tag('span', '<br />'.__('autocomplete_help'), array('class' => 'mobile_auto_complete_background'));
        $out .= content_tag('span', 'X', array('class' => 'mobile_auto_complete_escape'));
    }

    // following script will automatically intanciate geocode autocompleter
    $out .= javascript_queue('$.ajax({
      url: "' . minify_get_combined_files_url('/static/js/geocode_autocompleter.js') . '",
      dataType: "script",
      cache: true});');
    return $out;
}

function c2c_form_remote_add_element($url, $updated_success, $indicator = 'indicator', $removed_id = null)
{
    $url = url_for($url);

    $js = "$('#indicator').show();" .
          "$.post('$url', $(this).serialize())" .
            ".always(function() { $('#indicator').hide(); })" .
            ".fail(function(data) { C2C.showFailure(data.responseText); })" .
            ".success(function(data) {" .
              "$('#$updated_success').append(data);" .
              "if ($('#${updated_success}_rsummits_name').val('').length) {" .
                "$('#${updated_success}_associated_routes". ($removed_id ? ", #$removed_id" : '') ."').hide();" .
              "}" .
            "});" .
            "return false;";

    $options['action'] = $url;
    $options['method'] = isset($options['method']) ? $options['method'] : 'post';
    $options['onsubmit'] = $js;

    return tag('form', $options, true);
}

function c2c_link_to_delete_element($link_type, $main_id, $linked_id, $main_doc = true,
                                    $strict = 1, $updated_failure = null, $indicator = 'indicator',
                                    $tips = null)
{
    $response = sfContext::getInstance()->getResponse()->addJavascript('/static/js/rem_link.js', 'last');
    // NB : $del_image_id is for internal use, but will be useful when we have several delete forms in same page
    $main_doc = ($main_doc) ? 'true' : 'false';
    $tips = ($tips == null) ? 'Delete this association' : $tips;
    return link_to(picto_tag('action_del_light', __($tips)), '#',
                         array('onclick' => "C2C.remLink('$link_type', $main_id, $linked_id, $main_doc, $strict); return false;"));
}


/**
 * Create a form that allow to link the current document with several kinds of other docs
 *
 * @param module current document module
 * @param id current document id
 * @param modules_list list of modules available for association
 * @param default_selected default selected module in the dropdown list
 * @param field_prefix used to prevent to have ids conflict when multiple forms
 * @param $options
 *   $hide if true, display button to hide/show the form + some text
 *   $indicator, the ID of the HTML object used to display indications on the ajax status (Loading, Success, ...)
 *   $removed_id, the ID of the HTML object to hide
 *   $suggest_near_docs, for suggesting docs based on geolocalization
 *   $suggest_friends, for suggesting people you go out with most
 *
 * FIXME js code is quite messy, would be great to move most of it into separate js file
 */
function c2c_form_add_multi_module($module, $id, $modules_list, $default_selected, $options)
{
    // optional values
    $field_prefix = _option($options, 'field_prefix', 'list_associated_docs');
    $hide = _option($options, 'hide', true);
    $indicator = _option($options, 'indicator', 'indicator');
    $removed_id = _option($options, 'removed_id');
    $suggest_near_docs = _option($options, 'suggest_near_docs', false);
    $suggest_friends = _option($options, 'suggest_friends', false);

    $conf = sfConfig::get('app_modules_list');
    $modules_list = array_intersect($conf, $modules_list);
    $modules_list_i18n = array_map('__', $modules_list);
    // modules for which lookig for neighboors has some sense
    $near_docs_modules_list = array_intersect($conf, array('huts', 'parkings', 'sites', 'summits', 'routes'));
    $near_docs_module_ids_list = array_keys($near_docs_modules_list);

    // for site-site, parking-parking or summit-summit associations, be explicit about association direction
    if (in_array($module, array('sites', 'parkings', 'summits')))
    {
        $modules_list_i18n[array_search($module, $modules_list)] = __('sub-' . $module);
    }

    // dropdown for choosing the type of docs to link
    $select_modules = select_tag('dropdown_modules',
        options_with_classes_for_select($modules_list_i18n, array($default_selected), array(), 'picto picto_'),
        array('class' => 'picto picto_' . $default_selected));
    
    $picto_add = ($hide) ? '' : picto_tag('picto_add', (in_array('users', $modules_list) ? __('Link an existing user or document') : __('Link an existing document'))) . ' ';
    
    $out = $picto_add . $select_modules;

    // js code fro autocompletion
    $js = "$('#dropdown_modules').change(function() {" .
            "var \$this = $(this), value = \$this.val(), indicator = $('#$indicator').show();" .
            "\$this.attr('class', 'picto picto_' + value);" .
            // retrieve the autocomplete form for the selected module
            "$.get('" . url_for("/$module/getautocomplete") . "', 'module_id=' + value + '&field_prefix=$field_prefix" .
            ($suggest_near_docs ? "' + (['" . implode("','", $near_docs_module_ids_list) . "'].indexOf(value) > -1 ?
              '&extra_params=" . urlencode("lat=" . $suggest_near_docs['lat'] . "&lon=" . $suggest_near_docs['lon']) . "' : '')" : '\'') .
            ")" .
              ".always(function() { indicator.hide(); })" .
              ".done(function(data) { $('#${field_prefix}_form').html(data);";

    if ($suggest_near_docs || $suggest_friends)
    {
        $suggest_exclude = _option($options, 'suggest_exclude', []);

        // additional code for suggesting documents in the neighborhood or friends when relevant
        $js = "function getSuggestions() {" .
                "var input = $('#${field_prefix}_form').find('input[autocomplete=off]')," .
                    "module = input.attr('name').replace('_name', '')," .
                    "exclude = " . json_encode($suggest_exclude) . "," .
                    "suggestions_div = $('.autocomplete-suggestions').empty();" .
                "if (['" . implode("','", $near_docs_modules_list) . "'].indexOf(module) > -1 || module == 'users') {" .
                  // retrieve docs in neighborhood
                  "var params = (module == 'users') ? { id: $('#name_to_use').attr('data-user-id') } :" .
                    "{ lat:" . $suggest_near_docs['lat'] . ", lon:" . $suggest_near_docs['lon'] . " };" .
                  "if (input.not('[data-suggest-no-exclude]') && exclude[module] && exclude[module].length) {" .
                    "params['exclude'] = exclude[module].join(',');" .
                  "}" .
                  "$.getJSON('/'+module+'/suggest', params)" .
                    ".done(function(data) {" .
                      "if (data.length) suggestions_div.append('" . __('Suggestions: ') . "');" .
                      "$.each(data, function(index, obj) {" .
                        "suggestions_div.append($('<a href=\"'+obj.url+'\">'+obj.name+'</a>').click(function(e) {" .
                          "if (e.which !== 1) return;" . // only left click
                          "e.preventDefault();" .
                          "$('#${field_prefix}_form input[type=text]').triggerHandler('forceselect.autocomplete'," .
                                                                                     "$('<span id='+obj.id+'>'+obj.name+'</span>'));" .
                        "}), ' &nbsp;');" .
                      "});" .
                    "});" .
                "}" .
              "}" .
              "$('#${field_prefix}_form_association a').one('click', getSuggestions);" .
              $js;

        $js .= "getSuggestions();";
    }

    $js .= "});});";

    $out .= javascript_queue($js);

    // form start
    $out .= c2c_form_remote_add_element("$module/addAssociation?main_id=$id", $field_prefix, $indicator, $removed_id);

    // default form content
    $auto_complete_options = array('field_prefix' => $field_prefix);
    if ($suggest_near_docs && in_array($modules_list[$default_selected], $near_docs_modules_list))
    {
        $auto_complete_options['extra_params'] = "lat=" . $suggest_near_docs['lat'] . "&lon=" . $suggest_near_docs['lon'];
    }

    $out .= '<div id="' . $field_prefix . '_form' . '" class="ac_form">'
          . input_hidden_tag('document_id', '0', array('id' => $field_prefix . '_document_id'))
          . input_hidden_tag('document_module', $modules_list[$default_selected], array('id' => $field_prefix . '_document_module'))
          . c2c_auto_complete($modules_list[$default_selected], $field_prefix . '_document_id',  $auto_complete_options)
          . '</div></form><div class="autocomplete-suggestions"></div>';

    // this is where the linked docs will be displayed after ajax
    $out = '<div class="doc_add">' . $out . '</div>';
    
    if ($hide)
    {
        $picto_add_rm = '<span class="assoc_img picto_add" title="' . __('show form') . '"></span>'
                   . '<span class="assoc_img picto_rm" title="' . __('hide form') . '"></span>';
        $picto_add_rm = link_to_function($picto_add_rm, "C2C.toggleForm('${field_prefix}_form')");
        
        $title = '<div id="_association_tool" class="section_subtitle extra" data-tooltip>' .
            (in_array('users', $modules_list) ? __('Link an existing user or document') :
                                                __('Link an existing document')) .
            __('&nbsp;:') . '</div> ';
        
        $pictos = ' ';
        foreach ($modules_list as $module)
        {
            $pictos .= picto_tag('picto_' . $module, __($module));
        }
        $pictos = link_to_function($pictos, "C2C.toggleForm('${field_prefix}_form')");
        $pictos = '<div class="short_data">' . $pictos . '</div>';
        
        $out = '<div class="one_kind_association empty_content">'
             . '<div class="association_tool hide" id="' . $field_prefix . '_form_association">'
             . $picto_add_rm
             . $title
             . $pictos
             . '<ul id="' . $field_prefix . '"><li style="display:none"></li></ul>'
             . $out
             . '</div></div>';
    }

    return $out;
}

function get_directly_linked_ids($docs)
{
    $a = array();
    foreach ($docs as $doc)
    {
        if (isset($doc['directly_linked']))
        {
            array_push($a, $doc['id']);
        }
    }

    return $a;
}
